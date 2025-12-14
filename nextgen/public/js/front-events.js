/* Simplified reservation modal script
   - Opens a centered modal when a `.reserve-btn` is clicked
   - Builds the form via DOM (no innerHTML reliance)
   - Submits via fetch to the reservation endpoint and shows feedback
*/
(function(){
    var modalId = 'reservationModalSimple';

    // Disable HTML5 native validation site-wide and convert certain input types to plain text
    function disableHTML5ValidationGlobally(){
        try{
            var forms = document.querySelectorAll('form');
            forms.forEach(function(f){
                try{ f.noValidate = true; f.setAttribute('novalidate','novalidate'); }catch(e){}
                var elems = f.querySelectorAll('input,textarea,select');
                elems.forEach(function(el){
                    // remove HTML5 validation attributes
                    ['required','min','max','pattern','step'].forEach(function(a){ if (el.hasAttribute && el.hasAttribute(a)) el.removeAttribute(a); });
                    // convert certain input types to text to avoid browser validation UI
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

    // run immediately and also watch for dynamically added forms (modals)
    disableHTML5ValidationGlobally();
    try{
        var mo = new MutationObserver(function(muts){
            muts.forEach(function(m){ if (m.addedNodes && m.addedNodes.length) disableHTML5ValidationGlobally(); });
        });
        mo.observe(document.documentElement || document.body, { childList: true, subtree: true });
    }catch(e){}

    function createModal(){
        if (document.getElementById(modalId)) return document.getElementById(modalId);
        var modal = document.createElement('div');
        modal.id = modalId;
        modal.className = 'reservation-modal-simple';

        var backdrop = document.createElement('div'); backdrop.className = 'rms-backdrop';
        var dialog = document.createElement('div'); dialog.className = 'rms-dialog';
        var close = document.createElement('button'); close.className = 'rms-close'; close.type = 'button'; close.innerHTML = '✕';
        var title = document.createElement('h3'); title.className = 'rms-title'; title.textContent = 'Réserver cet événement';
        var body = document.createElement('div'); body.className = 'rms-body';

        dialog.appendChild(close);
        dialog.appendChild(title);
        dialog.appendChild(body);
        modal.appendChild(backdrop);
        modal.appendChild(dialog);
        document.body.appendChild(modal);

        backdrop.addEventListener('click', closeModal);
        close.addEventListener('click', closeModal);
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
        return modal;
    }

    function openModal(evt){
        var modal = createModal();
        var body = modal.querySelector('.rms-body');
        body.innerHTML = '';

        var form = buildFormNode(evt);
        body.appendChild(form);

        document.body.classList.add('rms-no-scroll');
        modal.classList.add('open');
        // autofocus first input
        var first = form.querySelector('input[name="nom_complet"]'); if(first) first.focus();
    }

    function closeModal(){
        var modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.remove('open');
        document.body.classList.remove('rms-no-scroll');
        var body = modal.querySelector('.rms-body'); if (body) body.innerHTML = '';
    }

    function showPageNotice(data){
        try{
            var existing = document.querySelector('.rms-page-notice');
            if (existing) existing.parentNode.removeChild(existing);
        }catch(e){}
        // remove any active focus or text selection to avoid showing a caret in the notice
        try { if (document.activeElement && document.activeElement.blur) document.activeElement.blur(); } catch(e){}
        try { if (window.getSelection) { var s = window.getSelection(); if (s && s.removeAllRanges) s.removeAllRanges(); } } catch(e){}
        var n = document.createElement('div'); n.className = 'rms-page-notice rms-page-notice-center';
        var ic = document.createElement('div'); ic.className = 'rms-page-notice-icon'; ic.textContent = '🎉';
        var txt = document.createElement('div'); txt.className = 'rms-page-notice-text'; txt.textContent = data && data.message ? data.message : 'Votre réservation a été enregistrée.';
        var right = document.createElement('div'); right.className = 'rms-page-notice-right';
        // certificate link removed from page notice by request
        var close = document.createElement('button'); close.type='button'; close.className='rms-page-notice-close'; close.textContent='✕';
        // do not allow the close button to receive focus automatically (prevents caret)
        try { close.setAttribute('tabindex', '-1'); close.setAttribute('aria-label', 'Fermer la notification'); } catch(e){}
        close.addEventListener('click', function(){ if(n.parentNode) n.parentNode.removeChild(n); });
        n.appendChild(ic); n.appendChild(txt); n.appendChild(right); n.appendChild(close);
        document.body.appendChild(n);
        // auto-hide after 6s
        setTimeout(function(){ if(n.parentNode) n.parentNode.removeChild(n); }, 6000);
    }

    function buildFormNode(evt){
        var form = document.createElement('form');
        form.className = 'rms-form';
        form.method = 'post';
        var base = '';
        try {
            base = (typeof window !== 'undefined' && window.NEXTGEN_WEB_ROOT) ? window.NEXTGEN_WEB_ROOT : '';
        } catch (e) {
            base = '';
        }
        form.action = base + '/index.php?c=front&a=reservation';
        // Disable browser HTML5 validation entirely
        try { form.noValidate = true; form.setAttribute('novalidate', 'novalidate'); } catch(e) {}

        function row(labelText, input){
            var r = document.createElement('div'); r.className = 'rms-row';
            var l = document.createElement('label'); l.textContent = labelText; r.appendChild(l); r.appendChild(input);
            // field-level error container (for JS validation messages)
            var ferr = document.createElement('div'); ferr.className = 'rms-field-error'; ferr.setAttribute('aria-live','polite'); ferr.style.display = 'none';
            r.appendChild(ferr);
            return r;
        }

        function showFieldError(input, message){
            var row = input && input.parentNode;
            if (!row) return;
            var ferr = row.querySelector('.rms-field-error');
            if (!ferr) return;
            ferr.textContent = message;
            ferr.style.display = 'block';
            ferr.classList.remove('rms-field-warning');
            ferr.classList.add('rms-field-error-visible');
            input.classList.add('rms-input-invalid');
            // add animation
            ferr.classList.add('rms-animate');
            ferr.addEventListener('animationend', function a(){ ferr.classList.remove('rms-animate'); ferr.removeEventListener('animationend', a); });
        }

        function clearFieldError(input){
            var row = input && input.parentNode; if (!row) return;
            var ferr = row.querySelector('.rms-field-error'); if (!ferr) return;
            ferr.textContent = '';
            ferr.style.display = 'none';
            ferr.classList.remove('rms-field-error-visible');
            ferr.classList.remove('rms-field-warning');
            input.classList.remove('rms-input-invalid');
        }

        function showFieldWarning(input, message){
            var row = input && input.parentNode; if (!row) return;
            var ferr = row.querySelector('.rms-field-error'); if (!ferr) return;
            ferr.textContent = message;
            ferr.style.display = 'block';
            ferr.classList.remove('rms-field-error-visible');
            ferr.classList.add('rms-field-warning');
            ferr.classList.add('rms-animate');
            ferr.addEventListener('animationend', function b(){ ferr.classList.remove('rms-animate'); ferr.removeEventListener('animationend', b); });
        }

        function validateFormFields(form){
            var firstInvalid = null;
            var nomEl = form.querySelector('[name="nom_complet"]');
            var emailEl = form.querySelector('[name="email"]');
            var telEl = form.querySelector('[name="telephone"]');
            var placesEl = form.querySelector('[name="nombre_places"]');

            // clear all
            [nomEl,emailEl,telEl,placesEl].forEach(function(el){ if(el) clearFieldError(el); });

            // nom required
            if (nomEl) {
                if (!(nomEl.value || '').trim()) { showFieldError(nomEl, 'Ce champ est obligatoire'); if(!firstInvalid) firstInvalid = nomEl; }
            }

            // email: optional? assume required — validate format
            if (emailEl) {
                var v = (emailEl.value || '').trim();
                if (!v) { showFieldError(emailEl, 'Ce champ est obligatoire'); if(!firstInvalid) firstInvalid = emailEl; }
                else {
                    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!re.test(v)) { showFieldError(emailEl, 'Email non valide'); if(!firstInvalid) firstInvalid = emailEl; }
                }
            }

            // telephone: required + basic number characters
            if (telEl) {
                var tv = (telEl.value || '').trim();
                if (!tv) { showFieldError(telEl, 'Ce champ est obligatoire'); if(!firstInvalid) firstInvalid = telEl; }
                else {
                    var re2 = /^[0-9+()\-\s]{6,}$/;
                    if (!re2.test(tv)) { showFieldError(telEl, 'Numéro non valide'); if(!firstInvalid) firstInvalid = telEl; }
                }
            }

            // places: must be positive integer
            if (placesEl) {
                var pv = (placesEl.value || '').trim();
                if (!pv) { showFieldError(placesEl, 'Ce champ est obligatoire'); if(!firstInvalid) firstInvalid = placesEl; }
                else {
                    var num = parseInt(pv,10);
                    if (isNaN(num) || num <= 0) { showFieldError(placesEl, 'Nombre de places invalide'); if(!firstInvalid) firstInvalid = placesEl; }
                }
            }

            if (firstInvalid) { firstInvalid.focus(); return false; }
            return true;
        }

        // Validate a single field element (used for realtime validation)
        function validateFieldElement(el){
            if (!el) return true;
            clearFieldError(el);
            var name = el.name;
            var v = (el.value || '').trim();
            if (name === 'nom_complet'){
                if (!v) { showFieldError(el, "Champ non rempli"); return false; }
                return true;
            }
            if (name === 'email'){
                if (!v) { showFieldError(el, "Champ non rempli"); return false; }
                var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!re.test(v)) { showFieldError(el, 'Email non valide'); return false; }
                // if valid but not gmail, show a non-blocking suggestion
                var domain = (v.split('@')[1] || '').toLowerCase();
                if (domain && domain.indexOf('gmail.com') === -1){
                    showFieldWarning(el, 'Adresse Gmail recommandée (ex: utilisateur@gmail.com)');
                }
                return true;
            }
            if (name === 'telephone'){
                if (!v) { showFieldError(el, "Champ non rempli"); return false; }
                var re2 = /^[0-9+()\-\s]{6,}$/;
                if (!re2.test(v)) { showFieldError(el, 'Numéro de téléphone non valide'); return false; }
                return true;
            }
            if (name === 'nombre_places'){
                if (!v) { showFieldError(el, "Champ non rempli"); return false; }
                var num = parseInt(v,10);
                if (isNaN(num) || num <= 0) { showFieldError(el, 'Nombre de places invalide'); return false; }
                return true;
            }
            return true;
        }

        // Use plain text inputs (no HTML5-specific types/validation)
        var nom = document.createElement('input'); nom.type='text'; nom.name='nom_complet';
        var email = document.createElement('input'); email.type='text'; email.name='email';
        var tel = document.createElement('input'); tel.type='text'; tel.name='telephone';
        var places = document.createElement('input'); places.type='text'; places.name='nombre_places'; places.value='1';
        var msg = document.createElement('textarea'); msg.name='message'; msg.rows=3;

        form.appendChild(row('Nom complet', nom));
        form.appendChild(row('Email', email));
        form.appendChild(row('Téléphone', tel));
        form.appendChild(row('Nombre de places', places));
        form.appendChild(row('Message', msg));

        var hidId = document.createElement('input'); hidId.type='hidden'; hidId.name='id_evenement'; hidId.value = evt.id || '';
        var hidPoints = document.createElement('input'); hidPoints.type='hidden'; hidPoints.name='points_generes'; hidPoints.value = evt.points || 0;
        form.appendChild(hidId); form.appendChild(hidPoints);

        var feedback = document.createElement('div'); feedback.className = 'rms-feedback'; form.appendChild(feedback);

        var submit = document.createElement('button'); submit.type='submit'; submit.className='btn btn-primary'; submit.textContent = 'Envoyer la réservation';
        form.appendChild(submit);

        // attach realtime validation listeners to inputs (instant feedback)
        [nom,email,tel,places].forEach(function(el){
            if (!el) return;
            el.addEventListener('input', function(){ validateFieldElement(el); });
            el.addEventListener('change', function(){ validateFieldElement(el); });
        });

        form.addEventListener('submit', function(e){
            e.preventDefault();
            // client-side validation (JS only, no HTML5)
            if (!validateFormFields(form)) return;

            feedback.textContent = '';
            // show a subtle loading state
            var loader = document.createElement('div'); loader.className = 'rms-loading'; loader.textContent = 'Enregistrement...';
            feedback.appendChild(loader);
            var fd = new FormData(form);
            fetch(form.action, { method:'POST', body: fd }).then(function(resp){
                // If server returned non-2xx, try to read body text for error details
                if (!resp.ok) {
                    return resp.text().then(function(txt){
                        throw new Error(txt || ('HTTP error ' + resp.status));
                    });
                }
                // ensure response is JSON — attempt to parse, but provide clearer error if it fails
                var ct = resp.headers.get('Content-Type') || '';
                if (ct.indexOf('application/json') === -1) {
                    return resp.text().then(function(t){ throw new Error(t || 'Réponse inattendue du serveur'); });
                }
                return resp.json();
            }).then(function(data){
                // remove loader
                if (loader && loader.parentNode) loader.parentNode.removeChild(loader);
                if (data && data.success) {
                    // build success message
                    var success = document.createElement('div'); success.className = 'rms-success';
                    var icon = document.createElement('div'); icon.className = 'rms-success-icon'; icon.textContent = '✅';
                    var msg = document.createElement('div'); msg.className = 'rms-success-text'; msg.textContent = 'Votre réservation a bien été enregistrée ! Merci.';
                    success.appendChild(icon);
                    success.appendChild(msg);
                    // include certificate link if provided
                    // certificate link removed from modal success message by request
                    // optional places remaining
                    if (typeof data.places_restantes !== 'undefined') {
                        var placesInfo = document.createElement('div'); placesInfo.className = 'rms-places-info';
                        placesInfo.textContent = 'Places restantes : ' + data.places_restantes;
                        success.appendChild(placesInfo);
                    }
                    // If server returned points, update local points balance used by the front UI
                    try {
                        var pts = parseInt(data.points || 0, 10);
                        if (!isNaN(pts) && pts > 0) {
                            var key = 'nextgenPoints';
                            var current = parseInt(window.localStorage.getItem(key) || '0', 10);
                            var updated = (isNaN(current) ? 0 : current) + pts;
                            window.localStorage.setItem(key, updated);
                            // If the points page UI is present, update it immediately
                            try {
                                var pb = document.getElementById('pointsBalanceValue');
                                if (pb) pb.innerHTML = updated + ' pts';
                                var ph = document.getElementById('pointsHistory');
                                if (ph) ph.innerHTML = 'Dernière mise à jour : ' + new Date().toLocaleString();
                            } catch(e) {}
                            // include points info in the page notice message
                            data.message = (data.message || 'Votre réservation a été enregistrée.') + ' (' + pts + ' pts crédités)';
                        }
                    } catch(e) {}

                    // close modal immediately and show page-level notice after it's gone
                    closeModal();
                    // ensure modal is removed from DOM before showing notice
                    setTimeout(function(){ showPageNotice(data); }, 220);
                } else {
                    var err = document.createElement('div'); err.className = 'rms-error'; err.textContent = data && data.message ? data.message : 'Erreur lors de l\'enregistrement';
                    feedback.appendChild(err);
                }
            }).catch(function(err){
                if (loader && loader.parentNode) loader.parentNode.removeChild(loader);
                var errMsg = (err && err.message) ? err.message : 'Erreur réseau';
                // If server returned HTML, strip tags for brevity
                try { errMsg = errMsg.replace(/<[^>]*>/g, '').trim(); } catch(e) {}
                var errEl = document.createElement('div'); errEl.className = 'rms-error'; errEl.textContent = errMsg || 'Erreur réseau';
                feedback.appendChild(errEl);
            });
        });

        return form;
    }

    // attach handler globally
    document.addEventListener('click', function(e){
        var btn = e.target.closest && e.target.closest('.reserve-btn');
        if (!btn) return;
        e.preventDefault();
        var id = btn.getAttribute('data-event') || btn.getAttribute('data-id');
        var points = btn.getAttribute('data-points') || btn.getAttribute('data-points-generated') || 0;
        openModal({ id: id, points: points });
    }, false);

})();
