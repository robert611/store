let icons = document.getElementsByClassName('mark-icon');
let fullMark = document.getElementsByClassName('full-mark')[0];
let iconDescription = document.getElementById('mark-icon-description');
let markInput = document.getElementById('mark-input');
let opinionFormButton = document.getElementById('opinion-form-button');

Array.from(icons).forEach((icon) => {
    icon.addEventListener('click', (e) => {
        iconNumber = icon.getAttribute('data-value');

        fullMark.style.width = iconNumber * 20 + '%';
        iconDescription.textContent = icon.getAttribute('aria-label');
        markInput.value = iconNumber;
    });
})

opinionFormButton.addEventListener('click', (e) => {
    const mark = document.getElementById('mark-input').value;
    if (mark == "0") {
        e.preventDefault();

        iconDescription.innerHTML = "<span class='error-msg'>Musisz podać ocenę tego produktu.</span>";
    }
    else if(mark < 1 || mark > 5) {
        e.preventDefault();

        iconDescription.innerHTML = "<span class='error-msg'>Ocena musi znajdować się w przedziale od 1 do 5 gwiazdek.</span>";
    }
});