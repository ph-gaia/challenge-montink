<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/bootstrap.bundle.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="/assets/js/sb-admin-2.min.js"></script>

<script type="text/javascript">
    function resetForm() {
        var form = document.getElementById("form");
        if (form) {
            form.reset();
        }
    }
    $(document).ready(function() {

        $('#form').submit(function() {
            $('#resultado').html("Enviando...");

            var dados = $(this).serialize();
            $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: dados,
                success: function(data) {
                    $("#resultado").html(data);
                    resetForm();
                },
                error: function(xhr, status, error) {
                    $("#resultado").html(xhr.responseText);
                    resetForm();
                }
            });
            return false;
        });
    });
</script>
