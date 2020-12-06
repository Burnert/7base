document.addEventListener('DOMContentLoaded', () => {
  const locDeleteColumnPromise = sendInterfaceRequest('loc', { entry: 'delete_entry' });
  const locAutoPlaceholderPromise = sendInterfaceRequest('loc', { entry: 'auto' });
  let autoPlaceholderText = 'Auto';
  locAutoPlaceholderPromise.then(result => autoPlaceholderText = result);
  // For each entry view table on page
  document.querySelectorAll('.table-view.entry-view').forEach(view => {
    console.log(currentTable.foreignColumns);
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

    function makeNewEntriesArray(selector) {
      const rows = table.querySelectorAll(selector);
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
    const columnsRow = table.querySelector('tr.columns');
    const existingRows = table.querySelectorAll('.table-entry');

    function createFieldInput(column) {
      const columnName = column['Field'];
      const columnType = column['Type'];
      const columnKey = column['Key'];
      const columnNullable = column['Null'] == 'YES';
      const columnExtra = column['Extra'];
      const columnAutoInc = columnExtra.includes('auto_increment');
      // Select function based on type
      const inputCreator = inputTypes[Object.keys(inputTypes).find(key => columnType.includes(key))];
      if (inputCreator == undefined) console.log('No such input type', columnType);
      let maxlength;
      // If has max length
      if (columnType.includes('(')) {
        maxlength = columnType.substring(columnType.lastIndexOf('(') + 1, columnType.length - 1);
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
      const content = inputCreator(columnName, listener, attributes);
      if (columnAutoInc) {
        content.classList.add('vivid-placeholder');
      }
      const editEntriesAmt = table.querySelectorAll('.table-entry.edit').length - 1;

      return content;
    }

    const deletedEntries = [];
    const entriesToUpdate = [];
    // Add control buttons to existing rows if the table has any unique key
    if (currentTable.hasUniqueKey) {
      existingRows.forEach((tr, index) => {
        const btDelete = createTableFloatingButton('<i class="material-icons">delete</i>', { type: 'click', listener: () => {
          deletedEntries.push(currentTable.rows[index]);
          console.log(deletedEntries);
          tr.remove();
          showAdditionalButtons();
        }});
        tr.querySelector('td:first-of-type > div').appendChild(btDelete);
        const btEdit = createTableFloatingButton('<i class="material-icons">create</i>', { type: 'click', listener: (event) => {
          tr.classList.add('edit', 'update');
          event.target.remove();
          const fields = currentTable.rows[index];
          const fieldElements = tr.querySelectorAll('td span.value');
          // Loop through fields
          Object.keys(fields).forEach((fieldName, index) => {
            const field = fields[fieldName];
            const fieldElement = fieldElements[index];
            const column = currentTable.columns[index];

            const replacementInput = createFieldInput(column);
            replacementInput.value = field;
            fieldElement.replaceWith(replacementInput);

            if (column['Key'] == 'PRI') {
              entriesToUpdate.push(field);
            }
          });
          showAdditionalButtons();
        }});
        tr.querySelector('td:last-of-type > div').appendChild(btEdit);
      });
    }

    const tdCount = table.querySelectorAll('th').length;

    function addEntry() {
      const tr = table.insertRow(table.rows.length);
      tr.classList.add('table-entry', 'edit', 'add');
      // For each field
      for (let i = 0; i < tdCount; i++) {
        const td = tr.insertCell(i);
        const tdDiv = document.createElement('div');
        td.appendChild(tdDiv);

        const currentColumn = currentTable.columns[i];
        const content = createFieldInput(currentColumn);

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
        const bAddEntries = !!table.querySelectorAll('.table-entry.add').length;
        const bUpdateEntries = !!table.querySelectorAll('.table-entry.update').length;
        const bDeleteEntries = !!deletedEntries.length;
        // Resolve when all requests are complete
        let doneRequests = 0;
        const tryResolve = () => {
          if (++doneRequests == bAddEntries + bUpdateEntries + bDeleteEntries) {
            console.log(doneRequests);
            resolve();
          }
        };

        const nullRequiredFields = getNullRequiredFields();
        if (nullRequiredFields.length > 0) {
          nullRequiredFields.forEach(field => field.classList.add('error'));
        }
        else {
          // Add entries
          if (bAddEntries) {
            const entries = JSON.stringify(makeNewEntriesArray('.table-entry.add'));
            const columns = JSON.stringify(currentTable.columns.map(column => column['Field']));
            sendInterfaceRequest('add_entries', { name: currentTable.name, columns, entries }).then(result => {
              console.log(result);
              tryResolve();
            });
          }
          // Update entries
          if (bUpdateEntries) {
            const entriesOld = JSON.stringify(entriesToUpdate.map(id => currentTable.rows.find(row => row[currentTable.primaryKey] == id)));
            const entriesNew = JSON.stringify(makeNewEntriesArray('.table-entry.update'));
            sendInterfaceRequest('update_entries_unique', { name: currentTable.name, key: currentTable.primaryKey, entriesOld, entriesNew }).then(result => {
              console.log(result);
              tryResolve();
            });
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
        }
      });
      location.reload();
    });

    btCancel.addEventListener('click', () => location.reload());
  });
});
