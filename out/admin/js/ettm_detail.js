window.addEventListener('load', function() {
    var $addMoreBtn = document.getElementById('ettm-translate-more');


    $addMoreBtn.addEventListener('click', function() {
        var $counter = parseInt(document.getElementById('ettm-translate-more-counter').value);

        // Get last language pair
        var lastPair = document.getElementById('ettm-lang-selector-template').childNodes[1];
        var clonePair = lastPair.cloneNode(true);

        clonePair.querySelector('.ettm__start-lang').setAttribute(
            'name',
            'editlangs[' + $counter + '][start_lang]'
        );

        clonePair.querySelector('.ettm__target-lang').setAttribute(
            'name',
            'editlangs[' + $counter + '][target_lang]'
        );

        document.getElementById('ettm-translate-more-counter').value = $counter + 1;

        document.getElementById('ettm-lang-selector-container').appendChild(clonePair);
    });
});

function deleteThis( element ) {
    var toBeRemoved = element.parentNode.parentNode.parentNode;
    var parentNode = toBeRemoved.parentNode;
    parentNode.removeChild(toBeRemoved);
}
