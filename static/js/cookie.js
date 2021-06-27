/**
 * Encapsula métodos para guardar y obtener cookies
 */
class Cookie {
    /**
     * Obtiene una cookie identificada por su nombre
     * @param cname nombre de la cookie
     * @returns {string} valor de la cookie
     */
    static get(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    /**
     * Guarda una cookie.
     * @param cname clave de la cookie
     * @param cvalue valor de la cookie
     * @param exdays dias de expiración
     */
    static set(cname, cvalue, exdays) {
        let d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
}