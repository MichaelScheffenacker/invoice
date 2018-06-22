document.addEventListener('DOMContentLoaded', code);

function code() {
    let addButton = document.getElementById('add_task_button');
    addButton.addEventListener('click', () => addTaskRow());

    function addTaskRow() {
        let taskRows = document.getElementsByClassName('task-row');
        let lastTaskRow = taskRows[taskRows.length - 1];
        let newRow = lastTaskRow.cloneNode(true);
        let num = Number(newRow.getAttribute('number'));
        newRow.setAttribute('number', num + 1);
        Array.from(newRow.children).forEach(el => {
            el.setAttribute('aria-label', el.getAttribute('aria-label').replace(num, num+1));
            el.setAttribute('name', el.getAttribute('name').replace(num, num + 1));
        });
        lastTaskRow.parentElement.appendChild(newRow);

    }

}