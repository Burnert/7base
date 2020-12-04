function createTextInput(name, listener = null) {
  const input = document.createElement('input');
  input.type = 'text';
  input.name = name;
  if (listener) {
    input.addEventListener(listener.type, listener.listener);
  }
  return input;
}
let checkboxCounter = 0;
function createCheckboxInput(name, listener = null) {
  const label = document.createElement('label');
  const input = document.createElement('input');
  const span = document.createElement('span');
  input.type = 'checkbox';
  input.id = 'check' + checkboxCounter++;
  input.name = name;
  if (listener) {
    input.addEventListener(listener.type, listener.listener);
  }
  label.appendChild(input);
  label.appendChild(span);
  label.setAttribute('for', input.id);
  label.classList.add('custom-check');
  return label;
}
function createSelectInput(name, options, listener = null) {
  const input = document.createElement('select');
  input.name = name;
  options.forEach(opt => {
    const option = document.createElement('option');
    option.value = opt;
    option.innerHTML = opt;
    input.appendChild(option);
  });
  if (listener) {
    input.addEventListener(listener.type, listener.listener);
  }
  return input;
}

document.addEventListener('DOMContentLoaded', () => {
  // Menu & menu button link

  document.querySelectorAll('.menu-button').forEach(button => button.addEventListener('click', event => {
    const menuId = button.getAttribute('menu');
    const menu = document.getElementById(menuId);

    if (menu != undefined) {
      const menuActive = button.classList.contains('active');
      if (!menuActive) {
        button.classList.add('active');
        menu.classList.add('active');
      }
      else {
        button.classList.remove('active');
        menu.classList.remove('active');
      }
      localStorage.setItem('menuActive', !menuActive);
    }
  }));

  // Create table

  document.querySelectorAll('.create-container').forEach(create => {
    const form = create.querySelector('form');
    const properties = {
      name: () => createTextInput('name'),
      type: () => createSelectInput('type', [
        'INT',
        'VARCHAR',
        'TEXT',
        'DATE',
        'DECIMAL',
      ]),
      length_values: () => createTextInput('length_values'),
      default_value: (listener) => createSelectInput('default_value', [
        'None',
        'NULL',
        'Custom',
      ], { type: 'change', listener }),
      nullable: () => createCheckboxInput('nullable'),
      index: () => createSelectInput('index', [
        'None',
        'PRIMARY',
        'UNIQUE',
        'INDEX',
      ]),
      auto_increment: () => createCheckboxInput('auto_increment'),
    };

    const table = create.querySelector('.create-table > tbody');
    const tdCount = table.querySelectorAll('th').length;

    function addColumn() {
      const tr = table.insertRow(table.rows.length);
      tr.classList.add('table-column');
      // for each td
      for (let i = 0; i < tdCount; i++) {
        const td = tr.insertCell(i);
        const key = Object.keys(properties)[i];
        const listener = event => {
          if (event.target.name == 'default_value') {
            if (event.target.value == 'Custom') {
              
            }
            else {

            }
          }
        }
        const content = properties[key](listener);
        const columnsAmt = table.querySelectorAll('.table-column').length - 1;
        if (content.hasAttribute('name')) {
          content.name += '-' + columnsAmt;
        } 
        else {
          content.querySelector('input').name += '-' + columnsAmt;
        }
        if (i == 0) {
          const btDelete = document.createElement('button');
          btDelete.type = 'button';
          btDelete.classList.add('soft');
          btDelete.innerHTML = '<i class="material-icons">delete</i>';
          btDelete.addEventListener('click', () => {
            const columnRows = table.querySelectorAll('.table-column');
            if (columnRows.length > 1) {
              tr.remove();
              columnRows.forEach((row, index) => {
                row.querySelectorAll('input, select').forEach(input => {
                  input.name = input.name.split('-')[0] + '-' + index;
                });
              });
            }
          });
          td.appendChild(btDelete);
        }
        td.appendChild(content);
      }
    }

    const btAddColumn = create.querySelector('#b-add-column');
    btAddColumn.addEventListener('click', () => addColumn());

    addColumn();
  });
});
