$(document).ready(function () {
 
    // add the rule here
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
     return arg !== value;
    }, "Value must not equal arg.");
    
    /*
     * Enregistrement d'un nouveau client
     */
    $("#frmNewCustomer").validate({ 
        
        rules: {
            firstname:   { required: true },
            lastname:    { required: true },
            company_name :   { required: true },
            address :   { required: true },
            phones :  { required: true },
            email :  { required: true },
            login :  { required: true },
            pwd :  { required: true, minlength: 6 },
            confirmpwd :  { required: true, minlength: 6, equalTo : "#pwd" }
        },                
        messages: {
            firstname: "<div class='msg-error alert alert-error'>entrer votre nom</div>",
            lastname: "<div class='msg-error alert alert-error'>entrer votre prénoms</div>",
            company_name: "<div class='msg-error alert alert-error'> entrer la raison sociale </div>",
            address: "<div class='msg-error alert alert-error'>entrer votre adresse</div>",
            phones: "<div class='msg-error alert alert-error'>entrer votre téléphone </div>",
            email: "<div class='msg-error alert alert-error'>entrer votre email</div>",
            login: "<div class='msg-error alert alert-error'>entrer votre login</div>",
            pwd: { 
                required: "<div class='msg-error alert alert-error'>entrer votre mot de passe</div>",
                minlength: "<div class='msg-error alert alert-error'> 6 caractères minimum requis</div>"
            },
            confirmpwd: { 
                required: "<div class='msg-error alert alert-error'>confirmer votre mot de passe</div>",
                minlength: "<div class='msg-error alert alert-error'> 6 caractères minimum requis</div>",
                equalTo: "<div class='msg-error alert alert-error'> les mots de passe ne corresponde pas</div>"
            }
        },   
        
        submitHandler: function(form) {
            if(confirm("Etes vous sur de créer votre compte ? "))
                form.submit();
            else
                return false;
         
        }
    });
    
    
    /*
     * Enregistrement d'une reservation
     */
    $("#frmAddResa").validate({ 
        
        rules: {
            date_reunion:   { required: true },
            heure_debut:    { required: true },
            'meetingTime' :   { required: true, number:true },
            meetingType :   { required: true },
            meetingPlace :  { required: true },
           'nombreParticipants' :  { required: true, number:true  }
        },                
        messages: {
            date_reunion: "<div class='msg-error alert alert-error'>choisir une date de réservation</div>",
            heure_debut: "<div class='msg-error alert alert-error'>choisir l'heure de début</div>",
            'meetingTime': { 
                    required: "<div class='msg-error alert alert-error'>choisir la durée de la réunion</div>",
                    number: "<div class='msg-error alert alert-error'>saisir un nombre</div>"
             },
            meetingType: "<div class='msg-error alert alert-error'>choisir le type de la réunion</div>",
            meetingPlace: "<div class='msg-error alert alert-error'>choisir le site </div>",
            'nombreParticipants':{ 
                    required: "<div class='msg-error alert alert-error'>saisir le nombre maximum de participants </div>",
                    number: "<div class='msg-error alert alert-error'>saisir un nombre</div>"
             }
         },   
        
        submitHandler: function(form) {
            if(confirm("Etes vous sur de soumettre cette demande de réservation ? "))
                form.submit();
            else
                return false;
         
        }
    });
    
   
    $('#heure_debut').timepicker({
        hourText: 'Heure'
    });
    
    
    
});



        
 