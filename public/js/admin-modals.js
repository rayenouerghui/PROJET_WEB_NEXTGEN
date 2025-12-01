/* Admin modals: load create/edit forms via AJAX and show in a centered modal
   - Intercepts links with class `btn-add` in admin pages
   - Fetches the page, extracts the first <form> and inserts it into a modal
   - Attaches the same `data-validate` handlers as the admin inline validator
*/
(function(){
    var modalId = 'adminModalSimple';

    function createAdminModal(){
        if (document.getElementById(modalId)) return document.getElementById(modalId);
        var modal = document.createElement('div'); modal.id = modalId; modal.className='reservation-modal-simple admin-modal';
        var backdrop = document.createElement('div'); backdrop.className='rms-backdrop';
        var dialog = document.createElement('div'); dialog.className='rms-dialog';
        var close = document.createElement('button'); close.className='rms-close'; close.type='button'; close.innerHTML='✕';
        var title = document.createElement('h3'); title.className='rms-title'; title.textContent='Formulaire';
        var body = document.createElement('div'); body.className='rms-body';
        dialog.appendChild(close); dialog.appendChild(title); dialog.appendChild(body);
        modal.appendChild(backdrop); modal.appendChild(dialog); document.body.appendChild(modal);
        backdrop.addEventListener('click', closeAdminModal);
        close.addEventListener('click', closeAdminModal);
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeAdminModal(); });
        return modal;
    }

    function openAdminModal(){
        var modal = createAdminModal(); modal.classList.add('open'); document.body.classList.add('rms-no-scroll');
    }
    function closeAdminModal(){ var m = document.getElementById(modalId); if(!m) return; m.classList.remove('open'); document.body.classList.remove('rms-no-scroll'); var b = m.querySelector('.rms-body'); if(b) b.innerHTML=''; }

    function attachAdminInlineValidation(form){
        if (!form) return;
        var fields = form.querySelectorAll('[data-validate]');
        function addError(el,msg){ var p = el.parentNode; var ex = p.querySelector('.field-error'); if(!ex){ ex = document.createElement('div'); ex.className='field-error'; p.appendChild(ex);} ex.textContent=msg; el.classList.add('is-invalid'); }
        function clearError(el){ var p = el.parentNode; var ex = p.querySelector('.field-error'); if(ex) ex.parentNode.removeChild(ex); el.classList.remove('is-invalid'); }
        function validateField(el){ var rules = (el.getAttribute('data-validate')||'').split(' ').filter(Boolean); var msg = el.getAttribute('data-msg')||'Ce champ est obligatoire.'; var val = (el.value||'').trim(); for(var i=0;i<rules.length;i++){ var r=rules[i]; if(r==='required'){ if(val===''){ addError(el,msg); return false; } } else if(r==='email'){ if(val!=='' ){ var re=/^[^<>()[\]\\.,;:\s@\"]+@[^<>()[\]\\.,;:\s@\"]+\.[^<>()[\]\\.,;:\s@\"]+$/i; if(!re.test(val)){ addError(el,msg); return false; } } } else if(r==='phone'){ if(val!==''){ var re2=/^[0-9+()\-\s]{6,}$/; if(!re2.test(val)){ addError(el,msg); return false; } } } else if(r==='number'){ if(val!=='' && isNaN(val)){ addError(el,msg); return false; } } else if(r==='date'){ if(val!=='' ){ var re3=/^\d{4}-\d{2}-\d{2}$/; if(!re3.test(val)){ addError(el,msg); return false; } } } } clearError(el); return true; }
        Array.prototype.forEach.call(fields,function(f){ f.addEventListener('input',function(){ validateField(f); }); f.addEventListener('change',function(){ validateField(f); }); });
        form.addEventListener('submit', function(e){ var first=null; Array.prototype.forEach.call(fields,function(f){ if(!validateField(f) && !first) first=f; }); if(first){ e.preventDefault(); first.focus(); } });
    }

    // intercept admin add links - bind on DOMContentLoaded for reliability
    function handleAddClick(e){
        e.preventDefault();
        var a = this;
        var href = a.getAttribute('href'); if(!href) return;
        // when loading admin forms, prefer a partial fragment if the server supports it
        var fetchHref = href;
        try{
            var url = new URL(href, window.location.origin);
            if (!url.searchParams.has('partial')) url.searchParams.set('partial','1');
            fetchHref = url.pathname + url.search;
        }catch(e){
            // fallback: append param
            fetchHref = href + (href.indexOf('?') === -1 ? '?partial=1' : '&partial=1');
        }
        // debug log
        try{ console.debug('admin-modals: loading', href); }catch(e){}
        fetch(fetchHref, { credentials:'same-origin' }).then(function(r){ return r.text(); }).then(function(html){
            var parser = new DOMParser(); var doc = parser.parseFromString(html,'text/html');
            // prefer an explicit form or form-card fragment rather than importing the full page
            var form = doc.querySelector('form');
            var fragment = form || doc.querySelector('#categoryForm') || doc.querySelector('#eventCreateForm') || doc.querySelector('.form-card') || doc.querySelector('.form-container') || doc.querySelector('main');
            var title = doc.querySelector('h1,h2,h3');
            var modal = createAdminModal(); var body = modal.querySelector('.rms-body'); body.innerHTML='';
            if (title) {
                // if the fragment itself contains a header, prefer that
                var fragTitle = (fragment && fragment.querySelector && fragment.querySelector('h1,h2')) ? fragment.querySelector('h1,h2').textContent : null;
                modal.querySelector('.rms-title').textContent = fragTitle || title.textContent || 'Formulaire';
            }
            if (fragment) {
                // import only the fragment (form or .form-card) into the modal
                var imported = document.importNode(fragment, true);
                try{ if (imported.tagName && imported.tagName.toLowerCase() === 'form') { imported.noValidate = true; imported.setAttribute('novalidate','novalidate'); } }catch(e){}
                body.appendChild(imported);
                // attach admin-like validation for dynamic form if it's a form or contains one
                var importedForm = (imported.tagName && imported.tagName.toLowerCase() === 'form') ? imported : imported.querySelector && imported.querySelector('form');
                if (importedForm) attachAdminInlineValidation(importedForm);
            } else {
                // fallback: insert whole body (last resort)
                var main = doc.body; body.appendChild(document.importNode(main,true));
            }
            openAdminModal();
        }).catch(function(err){ console.error('Erreur chargement formulaire', err); alert('Impossible de charger le formulaire'); });
    }

    function bindAddButtons(){
        var buttons = document.querySelectorAll('.btn-add');
        Array.prototype.forEach.call(buttons, function(b){ b.removeEventListener('click', handleAddClick); b.addEventListener('click', handleAddClick); });
    }

    document.addEventListener('DOMContentLoaded', function(){
        try{ bindAddButtons(); }catch(e){ console.error('admin-modals bind error', e); }
    });

    // keep delegated listener as fallback
    document.addEventListener('click', function(e){ var a = e.target.closest && e.target.closest('.btn-add'); if(!a) return; e.preventDefault(); handleAddClick.call(a, e); }, false);

})();
