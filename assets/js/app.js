$(document).ready(function(){
  // var textFontSize = badgeSize = "" ;
  var badge_type = '';
  var url = $('.baseUrl').val();
  var shop = $('.shop').val();
  var text_color = $('.getStyle').data('text_color');
  var label_color = $('.getStyle').data('label_color');
  var badge_color = $('.getStyle').data('badge_color');
  var label_font = $('.getStyle').data('label_font');
  var badge_font = $('.getStyle').data('badge_font');
  var text_font = $('.getStyle').data('text_font');
  var show_hide_label = $('.getStyle').data('show_hide_label');
  var max_width = $('.getStyle').data('max_width');
  var font = $('.getStyle').data('font_family');

  $('.uploadBadges').click(function(){
    $("#uploadModal").modal("hide");
    $("#myModal").modal("hide");
    $('#overlayer').show();
    $('#loading').show();
  });
  $('#insertBadges').click(function(){
    $("#myModal").modal("hide");
    $('.fixed-card').css('z-index',1);
    $('.lower-card').css('z-index',0);
    $('#overlayer').show();
    $('#loading').show();
    var insertBadges = [];
    $('.icon_arr').each(function(){
      if($(this).is(":checked"))
      {
        insertBadges.push($(this).val());
      }
    });
    insertBadges = insertBadges.toString();
    $.ajax({
      url:url+"Home/insertBadges?shop="+shop,
      method:"POST",
      data:{checkedData:insertBadges},
      success:function(data){
        $("#loading").delay(2000).fadeOut("slow");
        $("#overlayer").delay(2000).fadeOut("slow");
        ShopifyApp.flashNotice("Badge Inserted");
        $('.image-checkbox').removeClass('image-checkbox-checked');
        $(".icon_arr"). prop("checked", false);
        $('#loadbadges').empty();
        loadbadges();
      }
    });
  });
  //Badge type
  $('.transparent').click(function() {
    $('.bgtxtcolor').show();
    $('.bgcolor').hide();
    $('.badgeColor svg').css('background','transparent');
    badgesDesign("none,badgeColor");
    badgesDesign("svg,badge_type");
    $('.badge_type').val("svg");
    $('#loadbadges').empty();
    loadbadges();
  });
  $('.trans_with_back').click(function() {
    $('.bgtxtcolor').show();
    $('.bgcolor').show();
    badgesDesign("svg,badge_type");
    $('.badge_type').val("svg");
    $('#loadbadges').empty();
    loadbadges();
  });
  $('.trans_with_color').click(function() {
    $('.bgtxtcolor').hide();
    $('.bgcolor').hide();
    $('.badgeColor').css('background','transparent');
    badgesDesign("original,badge_type");
    badgesDesign("none,badgeColor");
    $('.badge_type').val("original");
    $('#loadbadges').empty();
    loadbadges();
  });
  $('.color_with_back').click(function() {
    $('.bgtxtcolor').hide();
    $('.bgcolor').show();
    badgesDesign("original,badge_type");
    $('.badge_type').val("original");
    $('#loadbadges').empty();
    loadbadges();
  });
  loadbadges();
  loadfont();
  function loadbadges(){
    var check_badge_font = $('.get_badge_size').val();
    var chck_val = $('.pickcolor').val();
    if (check_badge_font != '') {
      badge_font = $('.get_badge_size').val();
    }
    if (chck_val =z= '') {
      var badgeTextColor1 = $('.pickcolor').val();
    }else{
      badgeTextColor1 = $('#badgeTextColor').val();
    }
    $.ajax({
      url:url+"Home/loadbadges?shop="+shop,
      method:"GET",
      success:function(data){
        badge_type = $('.badge_type').val();
        var html = '';
        var badgelist = '';
        var parseData = JSON.parse(data);
        // console.log(parseData.svg_file.badge_name);
        badgelist += '<table id="tblCustomers">';
        badgelist += '<tbody>';
        var count = 1;
        $.each(parseData.data,function(index,value){
          badgelist += '<tr style="border-bottom: 1px solid #ddd;" data-badge_id="'+value.id+'" class="badge'+count+'">';
          badgelist += '<td style="padding: 10px;width:20%;">';
          badgelist += '<img width="45" height="45" src="'+url+'assets/upload/'+value.badge_image+'"><br>';
          badgelist += '</td>';
          badgelist += '<td style="position:relative;left:4px;width:35%;">';
          badgelist += '<lable class="badgeLable class'+value.id+'">'+value.badge_name+'</lable><div class="inputBadge class'+value.id+'" style="display:none"><input type="text" size="7" class="inputVlaue'+value.id+'" value="'+value.badge_name+'"></div>';
          badgelist += '</td>';
          badgelist += '<td class="add" style="width: 43%;">';
          badgelist += '<span style="cursor:pointer;"><i class="fa fa-caret-up iconstyle up"></i><span>';
          badgelist += '<span style="cursor:pointer;"><i class="fa fa-caret-down iconstyle down"></i><span>';
          badgelist += '<span style="cursor:pointer;" data-bid='+value.id+' class="editBadge"><i class="fa fa-edit iconstyle"></i></span><span style="display:none" style="cursor:pointer" data-id='+value.id+' class="insertIcon"><i style="font-size:18px!important;" class="fa fa-cloud-upload iconstyle"></i></span>';
          badgelist += '<span style="cursor:pointer;" class="deleteBadge" data-badgename='+value.badge_image+' data-badgenamepng='+value.original_image+' data-id='+value.id+'><i class="fa fa-trash iconstyle"></i><span>';
          badgelist += '</td>';
          badgelist += '</tr>';
          count++;
        });
        badgelist += '</table>';
        badgelist += '</tbody>';
        $('#badgeListTable').html(badgelist);
        if (badge_type == "original") {
          html += "<div class='vwul-div' style='display: flex;flex-wrap: wrap;justify-content: center;'>";
          $.each(parseData.data,function(index,value){
            html += "<div class='vwli-div' style='width: "+badge_font+"px; margin: 0px 7px 7px;'>";
            html += "<img class='badgeColor' style='border-radius: 6px;margin: auto;background: "+badge_color+";max-width: "+badge_font+"px' src="+url+"assets/upload/"+value.original_image+"><br>";
            html += "<p style='text-align:center;color: "+label_color+";font-size: "+label_font+"px;font-family: "+font+";display: "+show_hide_label+"' class='setLabelColor'>"+value.badge_name+"</p>";
            html += "</div>";
          });
          html += "</div>";
          $('#loadbadges').html(html);
        }
        else {
          html += "<div class='vwul-div' style='display: flex;flex-wrap: wrap;justify-content: center;'>";
          $.each(parseData.svg_file,function(index,value){
            html += "<div class='vwli-div' style='width: "+badge_font+"px; margin: 0px 7px 7px;'>";
            html += "<div class='badgeColor'  style='margin: auto;padding:0px;padding: 5px;'>"+value.svg+"</div>";
            html += "<p style='text-align:center;color: "+label_color+";font-size: "+label_font+"px;font-family: "+font+";display: "+show_hide_label+"' class='setLabelColor'>"+value.badge_name+"</p>";
            html += "</div>";
          });
          html += "</div>";
          $('#loadbadges').html(html);
          $("svg").attr("width",badge_font-10);
          $("svg").css("background",badge_color);
          $("svg").css("border-radius","6px");
          $("svg g path").attr("fill",badgeTextColor1);
          $("svg").attr("height","100%");
        }
      }
    });
  }
  $(document).on("click",".up,.down",function() {
    var row = $(this).parents("tr:first");
    if ($(this).is(".up")) {
      row.insertBefore(row.prev());
    } else if ($(this).is(".down")) {
      row.insertAfter(row.next());
    }
    var rowCount = $("#tblCustomers td").closest("tr").length;
    var indexArray = [];
    var badgeArray = [];
    for(var i=1;i<=rowCount;i++){
   	  var index = $("#tblCustomers .badge"+i).closest("tr").index();
      var badge_id = $('.badge'+i).data('badge_id');
      indexArray.push({order_id:index,id:badge_id});
    }
    $.ajax({
      url:url+"Home/updateOrder?shop="+shop,
      method:"POST",
      data:{index:indexArray},
      success:function()
      {
        $('#loadbadges').empty();
        loadbadges();
      }
    });
  });
  $(document).on('click','.deleteBadge',function(){
    $('#overlayer').show();
    $('#loading').show();
    var dataID = $(this).data('id');
    var badgename = $(this).data('badgename');
    var badgenamepng = $(this).data('badgenamepng');
    $.ajax({
      url:url+"Home/deleteBadge?shop="+shop,
      method:"POST",
      data:{dataID:dataID,badgename:badgename,badgenamepng:badgenamepng},
      success:function(data){
        $("#loading").delay(2000).fadeOut("slow");
        $("#overlayer").delay(2000).fadeOut("slow");
        ShopifyApp.flashNotice("Badge Deleted");
        $('#loadbadges').empty();
        loadbadges();
      }
    });
  });
  $(document).on('click','.editBadge',function(){
    var dataID = $(this).data('bid');
    // $('.badgeLable').show();
    $('.add').removeClass("bdg_setting");
    $('.add').addClass("bdg_setting-after");
    $('badgeLable.class'+dataID).hide();
    $('.editBadge').hide();
    $('.class'+dataID).show();
    $('.insertIcon').show();
  });
  $(document).on('click','.insertIcon',function(){

    $("#loading").show();
    $("#overlayer").show();
    $('.add').removeClass("bdg_setting-after");
    $('.add').addClass("bdg_setting");
    var dataID = $(this).data('id');
    var inputVlaue = $('.inputVlaue'+dataID).val();
    // console.log(inputVlaue,dataID);
    $.ajax({
      url:url+"Home/editBadge?shop="+shop,
      method:"POST",
      data:{inputVlaue:inputVlaue,dataID:dataID},
      success:function(data){
        $("#loading").delay(2000).fadeOut("slow");
        $("#overlayer").delay(2000).fadeOut("slow");
        ShopifyApp.flashNotice("Badge Updated");
        $('#loadbadges').empty();
        loadbadges();
      }
    });
  });
  $('.openModal').click(function(){
    ('#uploadModal').modal("show");
  });

  function badgesDesign(elementFontSize)
  {
    $.ajax({
      url:url+"Home/badgesDesign?shop="+shop,
      method:"post",
      data:{elementFontSize:elementFontSize},
      success:function()
      {

      }
    });
  }

  //For Text Font Color
  $('#textColor').change(function(){
    var textColor = $('#textColor').val();
    $('.setTextColor').css("color",textColor);
    badgesDesign(textColor+",textColor");
  });
  //For Badge Background Color
  $('#badgeColor').change(function(){
    var badgeColor = $('#badgeColor').val();
    $('svg').css("background",badgeColor);
    $(".badgeColor").css("border-radius","6px");
    $('.badgeColor').css("background",badgeColor);
    badgesDesign(badgeColor+",badgeColor");
  });
  //For label Color
  $('#labelColor').change(function(){
    var labelColor = $('#labelColor').val();
    $('.setLabelColor').css("color",labelColor);
    badgesDesign(labelColor+",labelColor");
  });
  //For Set Text Value
  $('.setTextValue').change(function(){
    var setTextValue = $('.setTextValue').val();
    $('.setTextvalueData').html(setTextValue);
    badgesDesign(setTextValue+",setTextValue");
  });
  //For Set Font Label Family
  $('.setTextValue').change(function(){
    var setTextValue = $('.setTextValue').val();
    $('.setTextvalueData').html(setTextValue);
    badgesDesign(setTextValue+",setTextValue");
  });

    // Badge Font Size
    $('.badgeSize').change(function(){
      var badgeSize = $(this).val();
      if (badgeSize < 163) {
        $('.get_badge_size').val(badgeSize);
        $('.badgeColor').css('max-width',badgeSize+'px');
        $('.vwli-div').css('width',badgeSize+'px');
        $("svg").attr("width",badgeSize-10);
        $("svg").attr("height","100%");
        badgesDesign(badgeSize+",badgeSize");
      }
      else {
        var msg = 'You reached maximum badge size, badge size in between 0 to 162px.';
        $('#msg').html(msg).fadeIn(300).fadeOut(7000);
      }
    })
    // Label Font Size
    $('.labelFontSize').change(function(){
      var labelFontSize = $(this).val();
      $('.setLabelColor').css('font-size',labelFontSize+'px');
      badgesDesign(labelFontSize+",labelFontSize");
    })
    // Text Font Size
    $('.textFontSize').change(function(){
      var textFontSize = $(this).val();
      $('.setTextColor').css('font-size',textFontSize+'px');
      badgesDesign(textFontSize+",textFontSize");
    })

    function loadfont()
    {
      let xhr = new XMLHttpRequest();
  		xhr.open('GET', 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyAMk-1IS3gKNcbirtn_nAxd7QYPJZ4TfA8');
  		xhr.send();
  		xhr.onload = function() {
  		if (xhr.status != 200) {
  		   // alert(`Error ${xhr.status}: ${xhr.statusText}`); // e.g. 404: Not Found
  		} else {
  			var fontData = JSON.parse(xhr.response);
  	    var fontDataArray = fontData.items;
  	    var html ='';
  	    html += '<div class="dropdown">';
  			html +=	'<button style="width:100%;text-align:left" class="btn btn-default dropdown-toggle dropdown-toggle1" type="button" id="menu1" data-toggle="dropdown">Select Font Family</button>'
  			html += '<ul aria-labelledby="menu1" style="width: 100%; height: 300px; overflow: auto" class="dropdown-menu" id="font" role="menu">'
  	    for (x in fontDataArray) {
  				html += '<li role="presentation" class="getFont" data-fontname="'+fontDataArray[x].family+'" style="font-family: '+fontDataArray[x].family+'; font-weight: 200;padding: 12px 0px 0px 12px;">';
  				html += '<label for="'+fontDataArray[x].family+'">';
  				html += '<span class="font_style" font_style="'+fontDataArray[x].family+'">';
  				html += fontDataArray[x].family;
  				html += '</span>';
  				html += '</label>';
  				html += '</li>';
  			}
  			html+= '</ul>';
  			html+= '</div>';
  		  $('#loadFont').html(html);
  	    var html1 ='';
  	    html1 += '<div class="dropdown">';
  			html1 +=	'<button style="width:100%;text-align:left" class="btn btn-default dropdown-toggle dropdown-toggle2" type="button" id="menu1" data-toggle="dropdown">Select Font Family</button>'
  			html1 += '<ul aria-labelledby="menu1" style="width: 100%; height: 300px; overflow: auto" class="dropdown-menu" id="font" role="menu">'
  	    for (x in fontDataArray) {
  				html1 += '<li role="presentation" class="getFont1" data-fontname1="'+fontDataArray[x].family+'" style="font-family: '+fontDataArray[x].family+'; font-weight: 200;padding: 12px 0px 0px 12px;">';
  				html1 += '<label for="'+fontDataArray[x].family+'">';
  				html1 += '<span class="font_style" font_style="'+fontDataArray[x].family+'">';
  				html1 += fontDataArray[x].family;
  				html1 += '</span>';
  				html1 += '</label>';
  				html1 += '</li>';
  			}
  			html+= '</ul>';
  			html+= '</div>';
  		  $('#loadFont1').html(html1);
  		  }
  	};
    }
    $(document).on('click','.getFont',function(){
  		var fontName = $(this).data('fontname');
  		var getButton = $('.dropdown-toggle1')
  		getButton.html(fontName);
  		getButton.css("font-family",fontName);
      $('.setLabelColor').css('font-family',fontName);
      badgesDesign(fontName+",fontFamily");
  	});
    $(document).on('click','.getFont1',function(){
  		var fontName1 = $(this).data('fontname1');
  		var getButton1 = $('.dropdown-toggle2')
  		getButton1.html(fontName1);
  		getButton1.css("font-family",fontName1);
      $('.setTextColor').css('font-family',fontName1);
      badgesDesign(fontName1+",fontFamily1");
  	});
    $('.text-format-style').click(function(){
      var text_format = $(this).data('text_align');
      if (text_format == "center") {
        $('.text_right').removeClass("addstyle");
        $('.text_left').removeClass("addstyle");
      }else if (text_format == "right") {
        $('.text_left').removeClass("addstyle");
        $('.text_center').removeClass("addstyle");
      }else{
        $('.text_right').removeClass("addstyle");
        $('.text_center').removeClass("addstyle");
      }
      $('.setTextColor').css('text-align',text_format);
      $(this).removeClass('addstyle');
      badgesDesign(text_format+",text_format");
    });
    $('.boldCheckbox').click(function(){
      var bold_format = $('.boldStyle').data('text_align1');
      if($(this).prop("checked") == true){
        $('.setTextColor').css('font-weight',bold_format);
        $('.boldStyle').toggleClass('addstyle');
        badgesDesign(bold_format+",bold_text");
      }
      else if($(this).prop("checked") == false){
        $('.setTextColor').css('font-weight','unset');
        $('.boldStyle').toggleClass('addstyle');
        badgesDesign("unset,bold_text");
      }
    });
    //Show/Hide Badge Label
    $('.showBadgeLabel').click(function(){
      if($(this).prop("checked") == true){
        $('.setLabelColor').css('display','none');
        badgesDesign("none,show_hide_label");
      }
      else if($(this).prop("checked") == false){
        $('.setLabelColor').css('display','block');
        badgesDesign("block,show_hide_label");
      }
    });
    //Activate Badge
    $('.activateBadge').click(function(){
      if($(this).prop("checked") == true){
        badgesDesign("activate,activate_badge");
      }
       if($(this).prop("checked") == false){
        badgesDesign("deactivate,activate_badge");
      }
    });

    //Above text
    $('.above_text').click(function() {
      $('.above_text_show').show();
      $('.below_text_show').hide();
      // $('.setTextColor').css('position',"unset");
      // $('.setTextColor').css('bottom',"unset");
      // $('.setTextColor').css('width',"100%");
      badgesDesign("above_text,below_text");
    });
    //Below text
    $('.below_text').click(function() {
      $('.above_text_show').hide();
      $('.below_text_show').show();
      // $('.setTextColor').css('position',"absolute");
      // $('.setTextColor').css('bottom',"83px");
      // $('.setTextColor').css('width',"95%");
      badgesDesign("below_text,below_text");
    });


    //For Badge Text Color
    $('#badgeTextColor').change(function(){
      var badgeTextColor = $('#badgeTextColor').val();
      $('.pickcolor').val(badgeTextColor);
      $('svg g path').css("fill",badgeTextColor);
      $('#badgeTextColor').val(badgeTextColor);
      badgesDesign(badgeTextColor+",badgeTextColor");
    });

    //BADGE SETTINGS
    $('.clickEye1').click(function() {
      $(this).hide();
      $('.eyeSlash1').show();
      $('#loadbadges').hide();
      $('.setTextvalueData').hide();
      $('.hideSettings').hide();
      $('.hideSettings1').hide();
    });
    $('.eyeSlash1').click(function() {
      $(this).hide();
      $('.clickEye1').show();
      $('#loadbadges').show();
      $('.setTextvalueData').show();
      $('.hideSettings').show();
      $('.hideSettings1').show();
    });
    $('.open_modal').click(function() {
      $('.fixed-card').css('z-index','unset');
      $('.lower-card').css('z-index','unset');
    });
    $('.close-btn').click(function() {
      $('.fixed-card').css('z-index',1);
      $('.lower-card').css('z-index',0);
    });

});
