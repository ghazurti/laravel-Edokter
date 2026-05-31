<style>
    /* Transisi pindah tab: slide-up + fade halus */
    .card-tabs .tab-content > .tab-pane.fade {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .15s ease-out, transform .15s ease-out;
    }
    .card-tabs .tab-content > .tab-pane.fade.active.show {
        opacity: 1;
        transform: translateY(0);
    }
    /* Tab header: highlight aktif lebih jelas + hover halus */
    .card-tabs .nav-tabs .nav-link {
        transition: color .15s ease, background-color .15s ease, border-color .15s ease;
        border-top-left-radius: .35rem;
        border-top-right-radius: .35rem;
    }
    .card-tabs .nav-tabs .nav-link:not(.active):hover {
        background-color: #f4f6f9;
    }
    .card-tabs .nav-tabs .nav-link.active {
        font-weight: 600;
    }
</style>
