(function( $ ) {

	// USE STRICT
    "use strict";

    var ticket = {

        init : function (kong_helpdesk_settings) {

        	this.settings = kong_helpdesk_settings;
        	this.savedReplyButtonCreated = false;

            ticket.getSavedReplies();
            ticket.loadSavedReply();
            // ticket.showAttachmentFields();
            ticket.attachmentsLightbox();
            ticket.ticketNote();
        },
        showAttachmentFields : function() {

            // $('form#post')[0].encoding = 'multipart/form-data';

            // var reply = $('#wp-replycontent-wrap');
            // var html = '<p class="kong-helpdesk-attachments">' + 
            //               '<label for="author">Attachments</label>' + 
            //               '<input name="helpdesk-attachments[]" type="file" accept="image/*" multiple>' +
            //             '</p>';

            // reply.prepend(html);

        },
        attachmentsLightbox() {

            if(typeof LuminousGallery !== 'undefined') {
                new LuminousGallery($('#kong-helpdesk-attachments a.is-image'), {}, {
                    caption: function(trigger) {
                        return trigger.querySelector('img').getAttribute('alt');
                    }
                });
            }
        },
       	getSavedReplies : function() {

            var that = this;
       		var commentButton = $('#commentsdiv a.button');
       		var replySubmit = $('#replysubmit');
       		var spinner = $('#replysubmit .waiting spinner');

       		commentButton.on('click', function(e) {

       			var content = $('.wp-editor-container #content').text();
       			content = content.replace(/(<([^>]+)>)/ig,"");

       			if(that.savedReplyButtonCreated == false) {
                	that.savedReplyButtonCreated = true;

		            $.ajax({
	                    type : 'post',
	                    dataType : 'json',
	                    url : that.settings.ajax_url,
	                    data : {
	                        action : 'search_saved_replies',
	                        content: content
	                    },
	                    success : function( response ) {
	                        if(response.status == "true") {
	                        	
	                        	var sel = $('<select id="select-saved-reply" class="alignright">').appendTo(replySubmit);

	                        	sel.append($("<option>").text('Select a saved Reply').attr('value', ''));

	                        	if(response.suggessted_replies.length > 0) {
	                    			var optgroup = $("<optgroup>").attr('label', 'Suggested Replies').appendTo(sel);

									$(response.suggessted_replies).each(function() {
										optgroup.append($("<option>").attr('value', this.ID).text(this.post_title));
									});
	                        	}

	                        	if(response.all_replies.length > 0) {
	                    			var optgroup = $("<optgroup>").attr('label', 'All Replies').appendTo(sel);
	                    			
									$(response.all_replies).each(function() {
										optgroup.append($("<option>").attr('value', this.ID).text(this.post_title));
									});
	                        	}
	                        }
                    	}
                 	});
             	} else {
             		$('#select-saved-reply').trigger('change');
             	}
       		});
       	},
       	loadSavedReply : function() {

            var that = this;
       		var replySubmit = $('#replysubmit');
       		var spinner = $('#replysubmit .waiting spinner');

       		replySubmit.on('change', '#select-saved-reply', function(e) {

       			var option = $(this);
       			var replyID = option.val();
       			console.log(replyID);

       			if(replyID.length > 0) {

       				spinner.css('visibility', 'visible');
                    $.ajax({
	                    type : 'post',
	                    dataType : 'json',
	                    url : that.settings.ajax_url,
	                    data : {
	                        action : 'get_saved_reply',
	                        id : replyID,
	                    },
	                    success : function( response ) {
	                    	spinner.css('visibility', 'hidden');
	                    	if(response.status == "true") {
	                    		$('#replycontent').val(response.reply.post_content);
	                    	}
                    	}
                	});
       			}

       		});
       	},
        ticketNote : function() {

            var that = this;
            var ticketNoteButton = $('#add_ticket_note_button');

            ticketNoteButton.on('click', function(e) {
                e.preventDefault();

                var ticketNote = $('#ticket_note').val();
                if(ticketNote == "") {
                    return false;
                }

                var ticketID = $('#ticket_note_ticket_id').val();

                $.ajax({
                    type : 'post',
                    dataType : 'json',
                    url : that.settings.ajax_url,
                    data : {
                        action : 'create_ticket_note',
                        id : ticketID,
                        note : ticketNote
                    },
                    success : function( response ) {
                        $('.ticket_notes').append(response);
                        $('#ticket_note').val('');
                    }
                });

            });

            $('.ticket_notes').on('click', '.delete_note', function(e) {
                e.preventDefault();

                var noteID = $(this).data('id');
 
                var ticketID = $('#ticket_note_ticket_id').val();

                $.ajax({
                    type : 'post',
                    dataType : 'json',
                    url : that.settings.ajax_url,
                    data : {
                        action : 'delete_ticket_note',
                        id : ticketID,
                        noteID : noteID
                    },
                    success : function( response ) {
                        $('.ticket_notes #note-' + noteID).fadeOut();
                    }
                });

            });
        }
    };

    $(document).ready(function() {
        $('.tooltipped').tooltip();
    	if( $('body.post-type-ticket').length > 0) {
    		ticket.init(kong_helpdesk_settings);
        }
        
        if($("#primary_range").val()=='custom'){
            $("#primary_range").parents('.helpdesk-input').find('.date_range_cls').show();
        }else{
            $("#compare_range").parents('.helpdesk-input').find(".date_range_cls").hide();
        }
        if($("#compare_range").val()=='custom'){
            $("#compare_range").parents('.helpdesk-input').find('.date_range_cls').show();
        }else{
            $("#compare_range").parents('.helpdesk-input').find(".date_range_cls").hide();
        }
        
        
        jQuery("#primary_range,#compare_range").on("change",function(){
            //alert(jQuery(this).val());
            if(jQuery(this).val() == 'custom'){
                jQuery(this).parents('.helpdesk-input').find(".date_range_cls").show();
            }else{
                jQuery(this).parents('.helpdesk-input').find(".date_range_cls").hide();
            }
        });

        jQuery("#mail_template_select").on("change",function(){
            jQuery("#mail_template_mode").val("display");
            jQuery('#mail_template_frm').submit();
        });

        
	});

})( jQuery );
