/**
 * Encapsula los métodos para realizar peticiones
 * a la API REST de shodan.io
 */
class ShodanAPI {
    static search(query) {
        $.ajax({
            url: `/api/shodan.php?key=${Cookie.get("x-shodan-key")}&query=${query}`,
            method: "GET",
            beforeSend: function(xhr) {
                $("#code-button").text("Cargando...");
            },
            success: function (data, status) {
                data = JSON.parse(data);

                $("#code-button").removeClass("hide").text("¡Hecho!").attr("disabled", true);

                $(".result-textarea")
                    .val((data.count > 0) ?
                            JSON.stringify(data.data, null, 2) :
                            "¡Vaya, esa consulta no ha devuelto ningún resultado!" )
                    .removeAttr("hidden");

                $("#result-counter").text("La consulta ha devuelto " + data.count + " resultados");
            }
        });
    }
}