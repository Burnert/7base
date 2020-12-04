function createInput(name, attributes, listener = null) {
  const input = document.createElement('input');
  input.name = name;
  if (attributes) {
    Object.keys(attributes).forEach(key => {
      input.setAttribute(key, attributes[key]);
    });
  }
  if (listener) {
    input.addEventListener(listener.type, listener.listener);
  }
  return input;
}

function createTextInput(name, attributes, listener = null) {
  const input = createInput(name, attributes, listener);
  input.type = 'text';
  return input;
}

function createNumberInput(name, attributes, listener = null) {
  const input = createTextInput(name, attributes, listener);
  input.type = 'number';
  return input;
}

let checkboxCounter = 0;
function createCheckboxInput(name, attributes, listener = null) {
  const input = createInput(name, attributes, listener);
  const label = document.createElement('label');
  const span = document.createElement('span');
  input.type = 'checkbox';
  input.id = 'check' + checkboxCounter++;
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

function createTextArea(name, attributes, listener = null) {
  const input = document.createElement('textarea');
  input.name = name;
  if (attributes) {
    Object.keys(attributes).forEach(key => {
      input.setAttribute(key, attributes[key]);
    });
  }
  if (listener) {
    input.addEventListener(listener.type, listener.listener);
  }
  return input;
}

// Tables

function createTableFloatingButton(content, listener = null) {
  const button = document.createElement('button');
  button.type = 'button';
  button.classList.add('soft');
  button.innerHTML = content;
  if (listener) {
    button.addEventListener(listener.type, listener.listener);
  }
  return button;
}