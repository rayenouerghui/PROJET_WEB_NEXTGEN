/* Disable HTML5 validation site-wide
   - Converts certain input types to text and removes validation attributes
   - Observes DOM mutations to handle dynamically inserted forms
*/
(function(){
    function disableHTML5ValidationGlobally(){
        try{
            var forms = document.querySelectorAll('form');
            forms.forEach(function(f){
                try{ f.noValidate = true; f.setAttribute('novalidate','novalidate'); }catch(e){}
                var elems = f.querySelectorAll('input,textarea,select');
                elems.forEach(function(el){
                    ['required','min','max','pattern','step'].forEach(function(a){ if (el.hasAttribute && el.hasAttribute(a)) el.removeAttribute(a); });
                    if (el.tagName && el.tagName.toLowerCase() === 'input'){
                        var t = el.getAttribute('type');
                        if (t) {
                            var lower = t.toLowerCase();
                            var preserve = ['hidden','radio','checkbox','password','file','submit','button'];
                            var convertible = ['email','number','date','datetime-local','time','url','tel','month','week','color','range'];
                            if (convertible.indexOf(lower) !== -1 && preserve.indexOf(lower) === -1){
                                try{ el.setAttribute('type','text'); }catch(e){}
                            }
                        }
                    }
                });
            });
        }catch(e){}
    }

    // run immediately and watch for dynamic changes
    disableHTML5ValidationGlobally();
    try{
        var mo = new MutationObserver(function(muts){
            muts.forEach(function(m){ if (m.addedNodes && m.addedNodes.length) disableHTML5ValidationGlobally(); });
        });
        mo.observe(document.documentElement || document.body, { childList: true, subtree: true });
    }catch(e){}
})();
