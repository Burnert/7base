document.addEventListener('DOMContentLoaded', () => {
  const locDeleteColumnPromise = sendInterfaceRequest('loc', { entry: 'delete_entry' });
  const locAutoPlaceholderPromise = sendInterfaceRequest('loc', { entry: 'auto' });
  let autoPlaceholderText = 'Auto';
  locAutoPlaceholderPromise.then(result => autoPlaceholderText = result);
  // For each entry view table on page
  document.querySelectorAll('.table-view.entry-view').forEach(view => {
    const inputTypes = {
      int: (name, listener, attributes) => createNumberInput(name, attributes, { type: 'input', listener }),
      varchar: (name, listener, attributes) => createTextInput(name, attributes, { type: 'input', listener }),
      text: (name, listener, attributes) => {
        const textarea = createTextArea(name, attributes, { type: 'input', listener });
        textarea.addEventListener('DOMNodeInserted', () => textarea.style.minWidth = textarea.offsetWidth + 'px');
        return textarea;
      },
      date: (name, listener, attributes) => createDateInput(name, attributes, { type: 'input', listener }),
      decimal: (name, listener, attributes) => createNumberInput(name, attributes, { type: 'input', listener }),
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
        inputs.forEach(input => entryObj[input.name] = encodeURIComponent(input.value));
        entries.push(entryObj);
      });
      return entries;
    }

    const table = view.querySelector('tbody');
    const existingRows = table.querySelectorAll('.table-entry');

    const deletedEntries = [];
    // Add control buttons to existing rows
    existingRows.forEach((tr, index) => {
      const btDelete = createTableFloatingButton('<i class="material-icons">delete</i>', { type: 'click', listener: () => {
        // deletedEntryIds.push(currentTable.rows.splice(Array.from(existingRows).findIndex(row => row == tr), 1)[0]);
        deletedEntries.push(currentTable.rows[index]);
        console.log(deletedEntries);
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

        const currentColumn = currentTable.columns[i];
        const type = currentColumn['Type'];
        const columnKey = currentColumn['Key'];
        const columnNullable = currentColumn['Null'] == 'YES';
        const columnExtra = currentColumn['Extra'];
        const columnAutoInc = columnExtra.includes('auto_increment');
        // Select function based on type
        const inputCreator = inputTypes[Object.keys(inputTypes).find(key => type.includes(key))];
        if (inputCreator == undefined) console.log('No such input type', type);
        let maxlength;
        // If has max length
        if (type.includes('(')) {
          maxlength = type.substring(type.lastIndexOf('(') + 1, type.length - 1);
        }
        const listener = event => {
          event.target.classList.remove('error');
        }
        const attributes = { maxlength };
        if (!(columnNullable || columnAutoInc)) {
          attributes.required = true;
        }
        if (columnAutoInc) {
          attributes.placeholder = autoPlaceholderText;
        }
        const content = inputCreator(currentTable.columns[i]['Field'], listener, attributes);
        if (columnAutoInc) {
          content.classList.add('vivid-placeholder');
        }
        const editEntriesAmt = table.querySelectorAll('.table-entry.edit').length - 1;
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

    function getNullRequiredFields() {
      const requiredFields = table.querySelectorAll('input[required="true"]');
      return Array.from(requiredFields).filter(input => input.value == '');
    }
    
    const btAddEntry = view.querySelector('#b-add-entry');
    const btConfirm = view.querySelector('#b-confirm-add');
    const btCancel = view.querySelector('#b-cancel-add');

    btAddEntry.addEventListener('click', () => addEntry());

    btConfirm.addEventListener('click', async () => {
      await new Promise((resolve) => {
        const bAddEntries = !!table.querySelectorAll('.table-entry.edit').length;
        const bDeleteEntries = !!deletedEntries.length;
        console.log(bAddEntries, bDeleteEntries);
        // Resolve when all requests are complete
        let doneRequests = 0;
        const tryResolve = () => {
          if (++doneRequests == bAddEntries + bDeleteEntries) {
            console.log(doneRequests);
            resolve();
          }
        };
        // Add entries
        if (bAddEntries) {
          const nullRequiredFields = getNullRequiredFields();
          if (nullRequiredFields.length == 0) {
            const entries = JSON.stringify(makeNewEntriesArray());
            const columns = JSON.stringify(currentTable.columns.map(column => column['Field']));
            sendInterfaceRequest('add_entries', { name: currentTable.name, columns, entries }).then(result => {
              console.log(result);
              tryResolve();
            });
          }
          else {
            nullRequiredFields.forEach(field => field.classList.add('error'));
          }
        }
        // Delete entries if able to
        if (bDeleteEntries) {
          if (currentTable.hasUniqueKey) {
            if (currentTable.primaryKey) {
              const deletedUnique = JSON.stringify(deletedEntries.map(entry => entry[currentTable.primaryKey]));
              sendInterfaceRequest('delete_entries_unique', { name: currentTable.name, key: currentTable.primaryKey, entries: deletedUnique }).then(result => {
                console.log(result);
                tryResolve();
              });
            }
          }
        }
      });
      location.reload();
    });

    btCancel.addEventListener('click', () => location.reload());
  });
});
