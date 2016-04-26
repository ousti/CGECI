$(function() {
    var priceArray = "";
    
    // Form Add Objectifs Indicateur
    $("select[id='sel_univers_add_univers']").change(function(){
        var $loadingMessage = $("span#loadingMessage");
        $loadingMessage.css("display","block");
        $("select[id='sel_teb_add_eb']").empty();
        var sel_el = this.value; 
        var id_action = $("input[name='idaction']").val();
        $("input[name='action_indicateur_prix_unitaire']").val("");
        $.ajax({
            type: 'POST', 'format':'json',
            url: baseUrl+"/op/indicatorslisttoadd",
            data: { id_univers: sel_el, idaction: id_action },
            success:function(jsonData){ 
                priceArray = new Array();
                var contentString = '<option value=""> - choisir indicateur - </option>';
                for(var k = 0; k<jsonData.length; k++) {
                   var object = jsonData[k]; 
                   var id = object.id;
                   priceArray[id] = object['prixDefaut'];
                   contentString += "<option value='"+object.id+"'>"+object.indicateur+"</option>"; 
                } 
                $loadingMessage.css("display","none");
                $("select[id='sel_univers_add_indicateur']").html(contentString);                  
            }
        });
    });   
    $("select[id='sel_univers_add_indicateur']").change(function(){ 
        var sel_el = priceArray[this.value]; 
        $("input[name='action_indicateur_prix_unitaire']").val(sel_el);
        
    });
    
    
    // Form Add Element De Budget
    $("select[id='sel_teb_add_action']").change(function(){
        var $loadingMessage = $("span#loadingMessage");
        $loadingMessage.css("display","block");
        $("select[id='sel_teb_add_eb']").empty();
        var sel_el = this.value; 
        var id_action = $("input[name='idaction']").val();
        $("input[name='action_teb_prix_unitaire']").val("");
        $.ajax({
            type: 'POST', 'format':'json',
            url: baseUrl+"/estimation-budget-action/elementslisttoadd",
            data: { id_type_element_budget: sel_el, idaction: id_action },
            success:function(jsonData){ 
                priceArray = new Array();
                var contentString = '<option value=""> - choisir element de budget - </option>';
                for(var k = 0; k<jsonData.length; k++) {
                   var object = jsonData[k]; 
                   var id = object.id;
                   priceArray[id] = object['prixDefaut'];
                   contentString += "<option value='"+object.id+"'>"+object['element_budget']+"</option>"; 
                } 
                $loadingMessage.css("display","none");
                $("select[id='sel_teb_add_eb']").html(contentString);                  
            }
        });
    });   
    $("select[id='sel_teb_add_eb']").change(function(){ 
        var sel_el = priceArray[this.value]; 
        $("input[name='action_teb_prix_unitaire']").val(sel_el);
        
    });

});
