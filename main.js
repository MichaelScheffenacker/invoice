document.addEventListener('DOMContentLoaded', code);

function code() {
    let addButton = document.getElementById('add_lineitem_button');
    addButton.addEventListener('click', () => addLineitem());

    function addLineitem() {
        let lineitems = document.getElementsByClassName('lineitem');
        let lastLineitem = lineitems[lineitems.length - 1];
        let newLineitem = lastLineitem.cloneNode(true);
        let num = Number(newLineitem.getAttribute('data-number'));
        newLineitem.setAttribute('data-number', num + 1);
        Array.from(newLineitem.children).forEach(el => {
            el.setAttribute('aria-label', el.getAttribute('aria-label').replace(num, num+1));
            el.setAttribute('name', el.getAttribute('name').replace(num, num + 1));
        });
        lastLineitem.parentElement.appendChild(newLineitem);

    }

}