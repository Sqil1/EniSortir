window.onload = () => {

    //********   la date limite d'inscription doit être antérieure à la date de sortie   **********
    let dateSortie = document.getElementById("sortie_dateHeureDebut");
    let dateLimiteInscription = document.getElementById("sortie_dateLimiteInscription");
    dateSortie.addEventListener( "change", function() {
        let dateSortieValue = new Date(dateSortie.value);
        dateLimiteInscription.max = dateSortieValue.toISOString().split('T')[0];
    });
    //*********************************************************************************************


    //*************   chargement de la liste des lieux lors du choix d'une ville   ****************
    let ville =document.querySelector("#sortie_ville");
    ville.addEventListener("change", function(){
        let form = this.closest("form");
        let data = this.name + "=" + this.value;
        fetch( form.action, {
            method: form.getAttribute("method"),
            body: data,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;" +
                    "charset: utf-8"
            }
        })
            .then(response => response.text())
            .then( text => {
                const parser = new DOMParser();
                const html = parser.parseFromString(text, 'text/html');
                let nouveauSelect = html.querySelector("#sortie_lieu");
                document.querySelector("#sortie_lieu").innerHTML = nouveauSelect.innerHTML;
                //document.querySelector("#sortie_lieu").replaceWith(nouveauSelect);
                document.querySelector("#sortie_rue").value = '';
                document.querySelector("#sortie_codePostal").value = '';
                document.querySelector("#sortie_latitude").value = '';
                document.querySelector("#sortie_longitude").value = '';
            })
            .catch(error => {
                console.log(error);
            })
    });
    //*********************************************************************************************


    //***********  chargement de l'adresse du lieu choisi  ****************************************
    document.querySelector("#sortie_lieu").addEventListener("change", function(){
        let lieuId = document.querySelector("#sortie_lieu").value;
        //baseUrl se trouve dans le fichier base.html.twig
        let urlLieu = baseUrl + "/sortie/updateLieu?sortie_lieu=" + lieuId;
        fetch( urlLieu, {
            method: 'GET',
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then( response => response.json() )
            .then( data => {
                document.querySelector("#sortie_rue").value = data.rue;
                document.querySelector("#sortie_codePostal").value = data.codePostal;
                document.querySelector("#sortie_latitude").value = data.latitude;
                document.querySelector("#sortie_longitude").value = data.longitude;
            })
            .catch(error => {
                console.log(error);
            })
    });
    //*********************************************************************************************


    //*********************  clic sur le bouton annuler  ******************************************
    document.getElementById("boutonAnnuler").addEventListener("click", function() {
        //baseUrl se trouve dans le fichier base.html.twig
        window.location.href = baseUrl + "/sortie/liste";
    });
    //*********************************************************************************************

}