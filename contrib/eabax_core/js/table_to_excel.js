(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.tableToExcel = {
    attach: function attach(context) {
      var $context = $(context);
  
      if ($('table.table-to-excel').length === 0) {
        $('ul.action-links a.table-to-excel').parent().remove();
      }
      
      $('table.table-to-excel').once('table-to-excel').each(function () {
        if ($('ul.action-links a.table-to-excel').length === 0) {
          $('ul.action-links').append('<li><a class="table-to-excel btn btn-primary btn-sm">' + Drupal.t('Export') + '</a></li>');
        }
      });
  
      $('a.table-to-excel').once().each(function () {
        $(this).click(function () {
          var fileName = $('.box-header .box-title').text();
          
          var excelContent = $('table.table-to-excel').html();
          var excelFile = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
          excelFile += "<head><meta charset='UTF-8'/><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>";
          excelFile += "<body><table>";
          excelFile += excelContent;
          excelFile += "</table></body>";
          excelFile += "</html>";
          
          var link = "data:application/vnd.ms-excel;base64," + window.btoa(unescape(encodeURIComponent(excelFile)));
          var a = document.createElement("a");
          a.download = fileName+ ".xls";
          a.href = link;
          a.click();
          a.remove();
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
