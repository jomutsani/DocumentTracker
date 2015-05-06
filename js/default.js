$(document).ready(function(){
    $("#notif li a").click(function(){
      $(this).parent().hide(500);
    });
  
    $(document).on("pagecontainerbeforeshow", function(event,ui){
        //$("#<?php echo $bcodeid; ?>").barcode("<?php echo $r_barcodenumber; ?>","code39");
        generateBarcode();
    });
    
    generateBarcode();
});

function generateBarcode(){
    //$(".barcodeTarget").barcode($(this).prop("tagName"),"code39");
    $(".barcodeTarget").each(function(){
        $(this).barcode($(this).text(),"code39",{bgColor:"auto"});
    });
}