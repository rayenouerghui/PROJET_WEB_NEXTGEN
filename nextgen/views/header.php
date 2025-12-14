<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Administration - NextGen Events</title>
    <?php
    if (!defined('WEB_ROOT')) {
        require_once __DIR__ . '/../config/paths.php';
    }
    ?>
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/public/css/style.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/public/css/back.css" type="text/css" />
    <script src="<?php echo WEB_ROOT; ?>/public/js/disable-html5.js"></script>
    <script src="<?php echo WEB_ROOT; ?>/public/js/admin-modals.js"></script>
</head>
<body>
    <!-- Client-side validation script for admin forms (shows inline messages) -->
    <script type="text/javascript">
    (function(){
        function addError(el, message) {
            var parent = el.parentNode;
            var existing = parent.querySelector('.field-error');
            if (!existing) {
                var d = document.createElement('div');
                d.className = 'field-error';
                d.textContent = message;
                parent.appendChild(d);
            } else {
                existing.textContent = message;
            }
            el.classList.add('is-invalid');
        }

        function clearError(el) {
            var parent = el.parentNode;
            var existing = parent.querySelector('.field-error');
            if (existing) existing.parentNode.removeChild(existing);
            el.classList.remove('is-invalid');
        }

        function validateField(el) {
            var rules = (el.getAttribute('data-validate') || '').split(' ').filter(Boolean);
            var msg = el.getAttribute('data-msg') || 'Ce champ est obligatoire.';
            var val = (el.value || '').trim();
            for (var i=0;i<rules.length;i++){
                var r = rules[i];
                if (r === 'required') {
                    if (val === '') { addError(el, msg); return false; }
                } else if (r === 'email') {
                    if (val !== '') {
                        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@\"]+\.)+[^<>()[\]\\.,;:\s@\"]{2,})$/i;
                        if (!re.test(val)) { addError(el, msg); return false; }
                    }
                } else if (r === 'phone') {
                    if (val !== '') {
                        var re2 = /^[0-9+()\-\s]{6,}$/;
                        if (!re2.test(val)) { addError(el, msg); return false; }
                    }
                } else if (r === 'number') {
                    if (val !== '') {
                        if (isNaN(val)) { addError(el, msg); return false; }
                    }
                } else if (r === 'date') {
                    if (val !== '') {
                        var re3 = /^\d{4}-\d{2}-\d{2}$/;
                        if (!re3.test(val)) { addError(el, msg); return false; }
                    }
                }
            }
            clearError(el);
            return true;
        }

        document.addEventListener('DOMContentLoaded', function(){
            var forms = document.querySelectorAll('form.validate-admin');
            Array.prototype.forEach.call(forms, function(form){
                // attach input/change listeners to clear errors
                var fields = form.querySelectorAll('[data-validate]');
                Array.prototype.forEach.call(fields, function(f){
                    f.addEventListener('input', function(){ validateField(f); });
                    f.addEventListener('change', function(){ validateField(f); });
                });

                form.addEventListener('submit', function(e){
                    var firstInvalid = null;
                    Array.prototype.forEach.call(fields, function(f){
                        var ok = validateField(f);
                        if (!ok && !firstInvalid) firstInvalid = f;
                    });
                    if (firstInvalid) {
                        e.preventDefault();
                        firstInvalid.focus();
                    }
                });
            });
        });
    })();
    </script>

    <!-- Admin layout: sidebar + main -->
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>NextGen Admin</h2>
            <nav class="sidebar-menu">
                <a class="item active" href="<?php echo WEB_ROOT; ?>/index.php?c=admin&amp;a=dashboard">
                    <span>Dashboard</span>
                </a>
                <a class="item" href="<?php echo WEB_ROOT; ?>/index.php?c=categorie&amp;a=index">
                    <span>Catégories</span>
                </a>
                <a class="item" href="<?php echo WEB_ROOT; ?>/index.php?c=evenement&amp;a=index">
                    <span>Événements</span>
                </a>
                <a class="item" href="<?php echo WEB_ROOT; ?>/index.php?c=reservation&amp;a=index">
                    <span>Réservations</span>
                </a>
            </nav>
            <div class="sidebar-actions">
                <a href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=index">Voir le Site</a>
            </div>
        </aside>

        <div class="admin-main">

