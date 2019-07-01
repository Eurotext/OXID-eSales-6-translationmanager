window.addEventListener('load', function() {
    var $addMoreBtn = document.getElementById('ettm-translate-more');

    if ($addMoreBtn) {
        $addMoreBtn.addEventListener('click', function() {
            var $counter = parseInt(document.getElementById('ettm-translate-more-counter').value);

            // Get last language pair
            var lastPair = document.getElementById('ettm-lang-selector-template').childNodes[1];
            var clonePair = lastPair.cloneNode(true);

            clonePair.querySelector('.ettm__target-lang-selector').setAttribute(
                'name',
                'editlangs[' + $counter + ']'
            );

            document.getElementById('ettm-translate-more-counter').value = $counter + 1;

            document.getElementById('ettm-lang-selector-container').appendChild(clonePair);
            disableTargetLangs();
        });
    }    
});

function disableTargetLangs() {
    var selectedLangsJson = document.querySelector('#ettm-translate-selection');
    console.log(selectedLangsJson);
}

function deleteThis( element ) {
    var toBeRemoved = element.parentNode;
    var parentNode = toBeRemoved.parentNode;
    parentNode.removeChild(toBeRemoved);
}
