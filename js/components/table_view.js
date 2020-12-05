document.addEventListener('DOMContentLoaded', () => {
  const locDeleteColumnPromise = sendInterfaceRequest('loc', { entry: 'delete_entry' });
  // For each entry view table on page
  document.querySelectorAll('.table-view.entry-view').forEach(view => {
    const inputTypes = {
      int: (name, listener, attributes) => createNumberInput(name, attributes, { type: 'change', listener }),
      varchar: (name, listener, attributes) => createTextInput(name, attributes, { type: 'change', listener }),
      text: (name, listener) => {
        const textarea = createTextArea(name, {}, { type: 'change', listener });
        textarea.addEventListener('DOMNodeInserted', () => textarea.style.minWidth = textarea.offsetWidth + 'px');
        return textarea;
      },
      date: (name, listener) => createDateInput(name, {}, { type: 'change', listener }),
      decimal: (name, listener) => createNumberInput(name, {}, { type: 'change', listener }),
    };

    function showAdditionalButtons() {
      view.querySelectorAll('#b-confirm-add, #b-cancel-add').forEach(button => button.style.display = 'block');
    }

    function makeNewEntriesArray() {
      const rows = table.querySelectorAll('.table-entry.edit');
      const entries = [];
      rows.forEach(row => {
        const entryObj = {};
        const inputs = Array.from(row.querySelectorAll('input, select, textarea')).filter(element => element.name);
        inputs.forEach(input => entryObj[input.name] = input.value);
        entries.push(entryObj);
      });
      return entries;
    }

    const table = view.querySelector('tbody');
    const existingRows = table.querySelectorAll('.table-entry');

    const deletedEntryIds = [];
    // Add control buttons to existing rows
    existingRows.forEach(tr => {
      const btDelete = createTableFloatingButton('<i class="material-icons">delete</i>', { type: 'click', listener: () => {
        deletedEntryIds.push(currentTableRows.splice(Array.from(existingRows).findIndex(row => row == tr), 1)[0]);
        console.log(deletedEntryIds);
        tr.remove();
        showAdditionalButtons();
      }});
      tr.querySelector('td:first-of-type > div').appendChild(btDelete);
      // const btEdit = 
    });

    const tdCount = table.querySelectorAll('th').length;

    function addEntry() {
      const tr = table.insertRow(table.rows.length);
      tr.classList.add('table-entry', 'edit');
      // For each field
      for (let i = 0; i < tdCount; i++) {
        const td = tr.insertCell(i);
        const tdDiv = document.createElement('div');
        td.appendChild(tdDiv);

        const type = currentTableColumns[i]['Type'];
        const columnKey = currentTableColumns[i]['Key'];
        const columnExtra = currentTableColumns[i]['Extra'];
        // Select function based on type
        const inputCreator = inputTypes[Object.keys(inputTypes).find(key => type.includes(key))];
        if (inputCreator == undefined) console.log('No such input type', type);
        let maxlength;
        // If has max length
        if (type.includes('(')) {
          maxlength = type.substring(type.lastIndexOf('(') + 1, type.length - 1);
        }
        const listener = event => {

        }
        const attributes = { maxlength };
        if (columnExtra.includes('auto_increment')) {
          attributes.placeholder = 'Auto';
        }
        const content = inputCreator(currentTableColumns[i]['Field'], listener, attributes);
        const editEntriesAmt = table.querySelectorAll('.table-entry.edit').length - 1;
        // // Add index to name attr
        // if (content.hasAttribute('name')) {
        //   content.name += '-' + editEntriesAmt;
        // } 
        // else {
        //   content.querySelector('input').name += '-' + editEntriesAmt;
        // }
        // Insert control buttons in first td
        if (i == 0) {
          const btDelete = createTableFloatingButton('<i class="material-icons">delete</i>', { type: 'click', listener: () => {
            const entryRows = table.querySelectorAll('.table-entry.edit');
            tr.remove();
            entryRows.forEach((row, index) => {
              row.querySelectorAll('input, select, textarea').forEach(input => {
                input.name = input.name.split('-')[0] + '-' + index;
              });
            });
          }});
          locDeleteColumnPromise.then(result => btDelete.title = result);
          tdDiv.appendChild(btDelete);
        }
        tdDiv.appendChild(content);
      }
      showAdditionalButtons();
    }
    
    const btAddEntry = view.querySelector('#b-add-entry');
    const btConfirmAdd = view.querySelector('#b-confirm-add');
    const btCancelAdd = view.querySelector('#b-cancel-add');

    btAddEntry.addEventListener('click', () => addEntry());

    btConfirmAdd.addEventListener('click', () => {
      const entries = JSON.stringify(makeNewEntriesArray());
      sendInterfaceRequest('add_entries', { entries }).then(result => {
        console.log(result);
      });
      // const updateEntries;

    });

    btCancelAdd.addEventListener('click', () => location.reload());
  });
});
