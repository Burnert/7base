function createTextInput(name, listener = null) {
  const input = document.createElement('input');
  input.type = 'text';
  input.name = name;
  if (listener) {
    input.addEventListener(listener.type, listener.listener);
  }
  return input;
}

function createNumberInput(name, attributes, listener = null) {
  const input = createTextInput(name, listener);
  input.type = 'number';
  if (attributes) {
    Object.keys(attributes).forEach(key => {
      input.setAttribute(key, attributes[key]);
    });
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