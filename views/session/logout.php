<script>
window.addEventListener('load', function () {
    gapi.load('auth2', function () {
        gapi.auth2.init().then(function (auth2) {
            auth2.signOut();
            document.location.href = '/';
        });
    });
});
</script>
