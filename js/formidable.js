function frmFrontFormJS(){
	'use strict';
	var show_fields = [];
	var hide_later = [];
    var frm_checked_dep = [];

	function setNextPage(e){
		/*jshint validthis:true */
		var $thisObj = jQuery(this);
		var thisType = $thisObj.attr('type');
		if ( thisType != 'submit' ) {
			e.preventDefault();
		}

		var f = $thisObj.parents('form:first');
		var v = '';
		var d = '';
		var thisName = this.name;
		if ( thisName == 'frm_prev_page' || this.className.indexOf('frm_prev_page') !== -1 ) {
			v = jQuery(f).find('.frm_next_page').attr('id').replace('frm_next_p_', '');
		} else if ( thisName == 'frm_save_draft' || this.className.indexOf('frm_save_draft') !== -1 ) {
			d = 1;
		}

		jQuery('.frm_next_page').val(v);
		jQuery('.frm_saving_draft').val(d);

		if ( thisType != 'submit' ) {
			f.trigger('submit');
		}
	}
	
	function toggleSection(){
		/*jshint validthis:true */
		jQuery(this).next('.frm_toggle_container').slideToggle('fast');
		jQuery(this).toggleClass('active').children('.ui-icon-triangle-1-e, .ui-icon-triangle-1-s')
			.toggleClass('ui-icon-triangle-1-s ui-icon-triangle-1-e');
	}
    
    //Show "Other" text box when item is checked
	function showOtherText(){
		/*jshint validthis:true */
        if ( this.checked ) {
            var type = this.type;
            
            if ( type == 'radio' ) {
                jQuery(this).closest('.frm_radio').children('.frm_other_input').removeClass('frm_pos_none');
                jQuery(this).closest('.frm_radio').siblings().children('.frm_other_input')
                	.addClass('frm_pos_none').val('');
            } else if ( type == 'checkbox' ) {
                jQuery(this).closest('.frm_checkbox').children('.frm_other_input').removeClass('frm_pos_none');                
            }
        } else {
            //For checkboxes only
            jQuery(this).closest('.frm_checkbox').children('.frm_other_input').addClass('frm_pos_none').val('');
        }
	}

	function maybeCheckDependent(e){
		/*jshint validthis:true */
		var nameParts = this.name.replace('item_meta[', '').split(']');
		var field_id = nameParts[0];
		if ( ! field_id ) {
			return;
		}

		if ( jQuery('input[name="item_meta['+ field_id +'][form]"]').length ) {
			// this is a repeatable section with name: item_meta[370][0][414]
			field_id = nameParts[2].replace('[', '');
		}
		checkDependentField('und', field_id);
		doCalculation(e, field_id);
	}
	
	function checkDependentField(selected, field_id, rec){
		if(typeof(__FRMRULES) == 'undefined'){
			return;
		}
		
		var all_rules=__FRMRULES;
		var rules = all_rules[field_id];
		if ( typeof rules =='undefined'){
			return;
		}

		if ( typeof(rec) == 'undefined' || rec === null ) {
			//stop recursion?
			rec = 'go';
		}

        show_fields = []; // reset this variable after each click
		var this_opts = [];
		for ( var i = 0, l = rules.length; i < l; i++ ) {
		    var rule = rules[i];
		    if ( typeof rule != 'undefined' ) {
		        for ( var j = 0, rcl = rule.Conditions.length; j < rcl; j++ ) {
					var c = rule.Conditions[j];
					c.HideField = rule.Setting.FieldName;
					c.MatchType = rule.MatchType;
					c.Show = rule.Show;
		            this_opts.push(c);
		        }
		    }
		}
		
		var len = this_opts.length;
		for ( i = 0, l = len; i < l; i++ ) {
			hideOrShowField(i, this_opts[i], field_id, selected, rec);
			
			if ( i == (len-1) ) {
				hideFieldLater(rec);
			}
		}

	}

	function hideOrShowField(i, f, field_id, selected, rec){	
		if ( typeof show_fields[f.HideField] == 'undefined' ) { 
			show_fields[f.HideField] = [];
		}

		if ( f.FieldName != field_id || typeof selected == 'undefined' || selected == 'und' ) {
			if ( f.Type=='radio' || f.Type=='data-radio' ) {
				selected = jQuery('input[name="item_meta['+ f.FieldName +']"]:checked, input[type="hidden"][name="item_meta['+ f.FieldName +']"]').val();
			} else if ( f.Type=='select' || f.Type=='time' || f.Type=='data-select' ) {
				selected = jQuery('select[name^="item_meta['+ f.FieldName +']"], input[type="hidden"][name^="item_meta['+ f.FieldName +']"]').val();
				if ( jQuery('input[type="hidden"][name^="item_meta['+ f.FieldName +']"]').length ) {
					selected = [];
					jQuery('input[type="hidden"][name^="item_meta['+ f.FieldName +']"]').each(function(){
						selected.push(jQuery(this).val());
					});
				}
			} else if ( f.Type !='checkbox' && f.Type !='data-checkbox' ) {
				selected = jQuery('input[name^="item_meta['+ f.FieldName +']"], textarea[name^="item_meta['+ f.FieldName +']"]').val();
			}
		}

		if ( typeof selected == 'undefined' ) {
			selected = jQuery('input[type=hidden][name^="item_meta['+ f.FieldName +']"]').val();
			if ( typeof selected == 'undefined' ) {
				selected = '';
			}
		}

	    if ( f.Type=='checkbox' || (f.Type=='data-checkbox' && typeof(f.LinkedField)=='undefined') ) {
	        show_fields[f.HideField][i] = false;
            var checkVals = jQuery('input[name="item_meta['+ f.FieldName +'][]"]:checked, input[type="hidden"][name^="item_meta['+ f.FieldName +']"]');
            if ( checkVals.length ) {
                if ( f.Condition == '!=' ) {
                    show_fields[f.HideField][i] = true;
                }
                checkVals.each(function(){
        			var match = operators(f.Condition,f.Value,jQuery(this).val());
                    if ( f.Condition == '!=' ) {
                        if ( show_fields[f.HideField][i] === true && match === false ) {
                            show_fields[f.HideField][i] = false;
                        }
        			} else if(show_fields[f.HideField][i] === false && match){
        				show_fields[f.HideField][i] = true;
                    }
        		});
            } else {
    			var match = operators(f.Condition, f.Value, '');
    			if(show_fields[f.HideField][i] === false && match){
    				show_fields[f.HideField][i] = true;
                }
            }
	    } else if ( f.Type=='data-radio' ) {
			if ( typeof f.DataType == 'undefined' || f.DataType === '' || f.DataType === 'data' ) {
				if ( selected === '' ) {	
					show_fields[f.HideField][i] = false;
					jQuery(document.getElementById('frm_field_'+f.HideField+'_container')).fadeOut('slow');
					empty(document.getElementById('frm_data_field_'+f.HideField+'_container'));
				} else {
					if ( typeof f.DataType =='undefined' ) {
						show_fields[f.HideField][i] = operators(f.Condition, f.Value, selected);	
					} else {
						show_fields[f.HideField][i] = {'funcName':'getData','f':f,'sel':selected};
					}
				}
			} else {
				if ( selected === '' ) {
					show_fields[f.HideField][i] = false;
				} else {
					show_fields[f.HideField][i] = {'funcName':'getDataOpts','f':f,'sel':selected};
				}
			}
	    }else if(f.Type=='data-checkbox' && typeof f.LinkedField != 'undefined' ) {
			var checked_vals = [];
			jQuery('input[name="item_meta['+ f.FieldName +'][]"]:checked, input[type="hidden"][name="item_meta['+ f.FieldName +'][]"]').each(function(){checked_vals.push(jQuery(this).val());});
			if(typeof(f.DataType) == 'undefined' || f.DataType === '' || f.DataType === 'data'){
				if(checked_vals.length){
					show_fields[f.HideField][i] = true;
					empty(document.getElementById('frm_data_field_'+f.HideField+'_container'));
					getData(f,checked_vals,1);
					//jQuery.each(checked_vals, function(ckey,cval){getData(f,cval,1); });
				}else{
					show_fields[f.HideField][i] = false;
					jQuery(document.getElementById('frm_field_'+f.HideField+'_container')).fadeOut('slow');
					empty(document.getElementById('frm_data_field_'+f.HideField+'_container'));
				}
			}else{
				if(checked_vals.length){
					show_fields[f.HideField][i] = {'funcName':'getDataOpts','f':f,'sel':checked_vals};
				}else{
					show_fields[f.HideField][i] = false;
				}
	        }
	    } else if ( f.Type=='data-select' && typeof f.LinkedField != 'undefined' ) {
			if(f.DataType === '' || f.DataType == 'data'){
	            if(selected === ''){
					show_fields[f.HideField][i] = false;
					empty(document.getElementById('frm_data_field_'+f.HideField+'_container'));
				}else if(selected && jQuery.isArray(selected)){
					show_fields[f.HideField][i] = true;
					empty(document.getElementById('frm_data_field_'+f.HideField+'_container'));
					getData(f,selected,1);
				}else{
					show_fields[f.HideField][i] = {'funcName':'getData','f':f,'sel':selected};
				}
	        }else{
	            if(selected === ''){
					show_fields[f.HideField][i] = false;
				}else{
					show_fields[f.HideField][i] = {'funcName':'getDataOpts','f':f,'sel':selected};
				}
	        }
	    }else{
			if(typeof(f.Value)=='undefined' && f.Type.indexOf('data') === 0){
				if ( selected === '' ) {
					f.Value = '1';
				} else {
					f.Value = selected;
				}
				show_fields[f.HideField][i] = operators(f.Condition, f.Value, selected);
				f.Value = undefined;
			}else{
				show_fields[f.HideField][i] = operators(f.Condition, f.Value, selected);
			}
	    }

		hideFieldNow(i, f, rec);
	}

	function hideFieldNow(i, f, rec){
		if ( f.MatchType == 'all' ) {
			hide_later.push({
				'result':show_fields[f.HideField][i], 'show':f.Show,
				'match':f.MatchType, 'fname':f.FieldName, 'fkey':f.HideField
			});
			return;
		}

		if ( show_fields[f.HideField][i] !== false ) {
			if ( f.Show == 'show' ) {
				if ( show_fields[f.HideField][i] !== true ) {
					showField(show_fields[f.HideField][i], f.FieldName, rec);
				}else{
					var hideMe = document.getElementById('frm_field_'+f.HideField+'_container');
					if ( hideMe !== null ) {
						hideMe.style.display = '';
					}
				}
			}else{
				document.getElementById('frm_field_'+f.HideField+'_container').style.display = 'none';
			}
		}else{
			hide_later.push({
				'result':show_fields[f.HideField][i], 'show':f.Show,
				'match':f.MatchType, 'fname':f.FieldName, 'fkey':f.HideField
			});
		}
	}

	function hideFieldLater(rec){
		jQuery.each(hide_later, function(hkey,hvalue){ 
			if ( typeof hvalue != 'undefined' && typeof hvalue.result != 'undefined' ) {
                var container = document.getElementById('frm_field_'+hvalue.fkey+'_container');
                if ( container !== null ) {
                    if ( ( hvalue.match == 'any' && (jQuery.inArray(true, show_fields[hvalue.fkey]) == -1) ) ||
                        ( hvalue.match == 'all' && (jQuery.inArray(false, show_fields[hvalue.fkey]) > -1) ) ) {
                        if ( hvalue.show == 'show' ) {
                            jQuery(container).filter(':hidden').hide();
                            container.style.display = 'none';
                        } else {
                            container.style.display = '';
                        }
                    } else {
                        if ( hvalue.show == 'show' ) {
                            container.style.display = '';
                        } else {
                            jQuery(container).filter(':hidden').hide();
                            container.style.display = 'none';
                        }
                    }
                    if ( typeof hvalue.result !== false && typeof hvalue.result !== true ) {
                        showField(hvalue.result,hvalue.fname,rec);
                    }
                } else {
                    //console.log('no frm_field_'+hvalue.fkey+'_container');
                }
				delete hide_later[hkey];
			}
		});
	}

	function operators(op, a, b){
		if(typeof(b) == 'undefined'){
			b='';
		}
		if(jQuery.isArray(b) && jQuery.inArray(a,b) > -1){
			b = a;
		}
		if(String(a).search(/^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/) != -1){
			a = parseFloat(a);
			b = parseFloat(b);
		}
		if ( String(a).indexOf('&quot;') != '-1' && operators(op, a.replace('&quot;', '"'), b) ) {
			return true;
		}

		var theOperators = {
			'==': function(c,d){ return c == d; },
			'!=': function(c,d){ return c != d; },
			'<': function(c,d){ return c > d; },
			'>': function(c,d){ return c < d; },
			'LIKE': function(c,d){
				if(!d){
					/* If no value, then assume no match */
					return 0;
				}
				return d.indexOf(c) != -1;
			},
			'not LIKE': function(c,d){
				if(!d){
					/* If no value, then assume no match */
					return 1;
				}
				return d.indexOf(c) == -1;
			}
		};
		return theOperators[op](a, b);
	}

	function showField(funcInfo, field_id, rec){
		if ( funcInfo.funcName == 'getDataOpts' ) {
			getDataOpts(funcInfo.f, funcInfo.sel, field_id, rec);
		} else if ( funcInfo.funcName == 'getData' ) {
			getData(funcInfo.f, funcInfo.sel, 0);
		}
	}

	function getData(f,selected,append){
		var cont = document.getElementById('frm_data_field_'+f.HideField+'_container');
		if ( cont === null ) {
			return true;
		}

		if ( !append ) {
			cont.innerHTML = '<span class="frm-loading-img"></span>';
		}
		jQuery.ajax({
			type:'POST',url:frm_js.ajax_url,
			data:{action:'frm_fields_ajax_get_data', entry_id:selected, field_id:f.LinkedField, current_field:f.HideField},
			success:function(html){
				var fcont = document.getElementById('frm_field_'+f.HideField+'_container');
				if ( html !== '' ) {
					fcont.style.display = '';
				}
				
				if ( append ) {
					jQuery(cont).append(html);
				} else {
					cont.innerHTML = html;
					var val = jQuery(cont).children('input').val();
					if(html === '' || val === ''){
						fcont.style.display = 'none';
					}
					checkDependentField(selected, f.HideField);
				}
				return true;
			}
		});
	}

	function getDataOpts(f,selected,field_id,rec) {
		//don't check the same field twice when more than a 2-level dependency, and parent is not on this page
		if(rec == 'stop' && (jQuery.inArray(f.HideField, frm_checked_dep) > -1) && jQuery('input[type="hidden"][name^="item_meta['+ field_id +']"]').length){
			return;
		}

		var prev = [];
		if(f.DataType=='checkbox' || f.DataType=='radio'){
			jQuery('input[name^="item_meta['+ f.HideField +']"]:checked, input[type="hidden"][name^="item_meta['+ f.HideField +']"]').each(function(){prev.push(jQuery(this).val());});
		}else if(f.DataType == 'select'){
			var hiddenSelect = jQuery('input[type="hidden"][name^="item_meta['+ f.HideField +']"]');
			if(hiddenSelect.length){
				hiddenSelect.each(function(){
					prev.push(jQuery(this).val());
				});
			}else if(jQuery('select[name^="item_meta['+ f.HideField +']"]').length){
				prev = jQuery('select[name^="item_meta['+ f.HideField +']"]').val();
			}else if((rec == 'stop' || jQuery('#frm_data_field_'+ f.HideField +'_container .frm-loading-img').length) && (jQuery.inArray(f.HideField, frm_checked_dep) > -1)){
				return;
			}
		}else{
			prev.push(jQuery('input[name^="item_meta['+ f.HideField +']"]').val());
		}
		
		if(prev === null || prev.length === 0) prev = '';

		frm_checked_dep.push(f.HideField);

		//don't get values for fields that are to remain hidden on the page
		var $dataField = document.getElementById('frm_data_field_'+f.HideField+'_container');
		if($dataField === null && jQuery('input[type="hidden"][name^="item_meta['+ f.HideField +']"]').length){
			checkDependentField(prev, f.HideField, 'stop');
			return false;
		}

		if ( f.Value !== '' ) {
			var match = operators(f.Condition, f.Value, selected);
			if ( !match ) {
				document.getElementById('frm_field_'+f.HideField+'_container').style.display = 'none';
				document.getElementById('frm_data_field_'+f.HideField+'_container').innerHTML = '';
				checkDependentField('', f.HideField, 'stop');
				return false;
			}
		}

		$dataField.innerHTML = '<span class="frm-loading-img" style="visibility:visible;display:inline;"></span>';

		jQuery.ajax({
			type:'POST',url:frm_js.ajax_url,
			data:{action:'frm_fields_ajax_data_options', hide_field:field_id, entry_id:selected, selected_field_id:f.LinkedField, field_id:f.HideField},
			success:function(html){
				if(html === ''){
					document.getElementById('frm_field_'+f.HideField+'_container').style.display = 'none';
					prev='';
				}else if(f.MatchType!='all'){
					document.getElementById('frm_field_'+f.HideField+'_container').style.display = '';
				}
				
				$dataField.innerHTML = html;

				if(html !== '' && prev !== ''){
					if(!jQuery.isArray(prev)){
						var new_prev = [];
						new_prev.push(prev);
						prev = new_prev;
					}

					//select options that were selected previously			
					jQuery.each(prev, function(ckey,cval){
						if(typeof(cval) != 'undefined'){
							if(f.DataType == 'checkbox' || f.DataType == 'radio'){
								jQuery(document.getElementById('field_'+ f.HideField +'-'+ cval)).attr('checked','checked');
							}else if(f.DataType == 'select'){
								var selOpt = jQuery('select[name^="item_meta['+ f.HideField +']"] option[value='+ cval +']'); 
								if(selOpt.length){
									selOpt.prop('selected', true);
								}else{
									prev.splice(ckey,1); //remove options that no longer exist
								}
							}else{
								jQuery('input[name^="item_meta['+ f.HideField +']"]').val(cval);
							}
						}
					});
				}
				if(jQuery(html).hasClass('frm_chzn') && jQuery().chosen){
					jQuery('.frm_chzn').chosen({allow_single_deselect:true});
				}

				checkDependentField(prev, f.HideField, 'stop');
			}
		});
	}

	function doCalculation(e, field_id){
		if ( typeof __FRMCALC == 'undefined' ) {
			// there are no calculations on this page
			return;
		}

		var all_calcs = __FRMCALC;
		var calc = all_calcs.fields[field_id];
		if ( typeof calc == 'undefined' ) {
			// this field is not used in a calculation
			return;
		}

		var keys = calc.total;
		if ( e.frmTriggered && e.frmTriggered == field_id ) {
			return false;
		}

		var vals = [];
		var len = keys.length;
        var fCount = 0;

		// loop through each calculation this field is used in
		for ( var i = 0, l = len; i < l; i++ ) {
			var thisCalc = all_calcs.calc[keys[i]];
			var thisFullCalc = thisCalc.calc;

			// loop through the fields in this calculation
			fCount = thisCalc.fields.length;
			for ( var f = 0, c = fCount; f < c; f++ ) {
				var thisFieldId = thisCalc.fields[f];
				var thisField = all_calcs.fields[thisFieldId];
				var thisFieldCall = 'input'+ all_calcs.fieldKeys[thisFieldId];

				if ( thisField.type == 'checkbox' || thisField.type == 'select' ) {
					thisFieldCall = thisFieldCall +':checked,select'+ all_calcs.fieldKeys[thisFieldId] +' option:selected,'+ thisFieldCall+'[type=hidden]';
				} else if ( thisField.type == 'radio' || thisField.type == 'scale' ) {
					thisFieldCall = thisFieldCall +':checked,'+ thisFieldCall +'[type=hidden]';
				} else if ( thisField.type == 'textarea' ) {
				    thisFieldCall = thisFieldCall + ',textarea'+ all_calcs.fieldKeys[thisFieldId];
				}

                if ( typeof vals[thisFieldId] === 'undefined' || vals[thisFieldId] === 0 ) {
    				jQuery(thisFieldCall).each(function(){
    					if ( typeof vals[thisFieldId] === 'undefined' ) {
    						vals[thisFieldId] = 0;
    					}
    					var thisVal = jQuery(this).val();

    					if ( thisField.type == 'date' ) {
    						d = jQuery.datepicker.parseDate(all_calcs.date, thisVal);
    						if ( d !== null ) {
    							vals[thisFieldId] = Math.ceil(d/(1000*60*60*24));
    						}
    					}
                        var n = thisVal;
                        if ( n !== '' ){
    					    n = parseFloat(n.replace(/,/g,'').match(/-?[\d\.]+$/));
                        }

    					if ( typeof n === 'undefined' ) {
    						n = 0;
    					}
    					vals[thisFieldId] += n;
    				});
                }

				if ( typeof vals[thisFieldId] === 'undefined' ) {
					vals[thisFieldId] = 0;
				}

				thisFullCalc = thisFullCalc.replace('['+thisFieldId+']', vals[thisFieldId]);
			}

			// allow .toFixed for reverse compatability
			if ( thisFullCalc.indexOf(').toFixed(') ) {
				var calcParts = thisFullCalc.split(').toFixed(');
				if ( isNumeric(calcParts[1]) ) {
					thisFullCalc = 'parseFloat('+ thisFullCalc +')';
				}
			}

			var total = parseFloat(eval(thisFullCalc));
			if ( typeof total === 'undefined' ) {
				total = 0;
			}

			jQuery(document.getElementById('field_'+ keys[i])).val(total).trigger({
				type:'change', frmTriggered:keys[i], selfTriggered:true
			});
		}
	}

	function getFormErrors(object){
		jQuery(object).find('input[type="submit"], input[type="button"]').attr('disabled','disabled');
		jQuery(object).find('.frm_ajax_loading').addClass('frm_loading_now').css('visibility', 'visible');

		var jump = '';
		var newPos = 0;
		var cOff = 0;

		jQuery.ajax({
			type:'POST',url:frm_js.ajax_url,
			data:jQuery(object).serialize() +'&action=frm_entries_'+ jQuery(object).find('input[name="frm_action"]').val()+'&_ajax_nonce=1',
			success:function(errObj){
				errObj = errObj.replace(/^\s+|\s+$/g,'');
				if(errObj.indexOf('{') === 0){
					errObj = jQuery.parseJSON(errObj);
				}
				if(errObj === '' || !errObj || errObj === '0' || (typeof(errObj) != 'object' && errObj.indexOf('<!DOCTYPE') === 0)){
					var $loading = document.getElementById('frm_loading');
					if($loading !== null){
						var file_val=jQuery(object).find('input[type=file]').val();
						if(typeof(file_val) != 'undefined' && file_val !== ''){
							setTimeout(function(){
								jQuery($loading).fadeIn('slow');
							},2000);
						}
					}
					var $recapField = jQuery(object).find('.g-recaptcha');
					if($recapField.length && (jQuery(object).find('.frm_next_page').length < 1 || jQuery(object).find('.frm_next_page').val() < 1)){
                        $recapField.closest('.frm_form_field').replaceWith('<input type="hidden" name="recaptcha_checked" value="'+ frm_js.nonce +'">');
					}

					object.submit();
				}else if(typeof errObj != 'object'){
					jQuery(object).find('.frm_ajax_loading').removeClass('frm_loading_now').css('visibility', 'hidden');
					jump=jQuery(object).closest(document.getElementById('frm_form_'+jQuery(object).find('input[name="form_id"]').val()+'_container'));
					newPos=jump.offset().top;
					jump.replaceWith(errObj);
					cOff = document.documentElement.scrollTop || document.body.scrollTop;
					if(newPos && newPos > frm_js.offset && cOff > newPos){
						jQuery(window).scrollTop(newPos-frm_js.offset);
					}
					if(typeof(frmThemeOverride_frmAfterSubmit) == 'function'){
						var fin = jQuery(errObj).find('input[name="form_id"]').val();
						var p = '';
						if(fin) p = jQuery('input[name="frm_page_order_'+fin+'"]').val();
						frmThemeOverride_frmAfterSubmit(fin,p,errObj,object);
					}
					if(jQuery(object).find('input[name="id"]').length){
						var eid = jQuery(object).find('input[name="id"]').val();
						jQuery(document.getElementById('frm_edit_'+eid)).find('a').addClass('frm_ajax_edited').click();
					}
				}else{
					jQuery(object).find('input[type="submit"], input[type="button"]').removeAttr('disabled');
					jQuery(object).find('.frm_ajax_loading').removeClass('frm_loading_now').css('visibility', 'hidden');

					//show errors
					var cont_submit=true;
					jQuery('.form-field').removeClass('frm_blank_field');
					jQuery('.form-field .frm_error').replaceWith('');
					jump = '';
					var show_captcha = false;
                    var $fieldCont = null;
					for (var key in errObj){
						$fieldCont = jQuery(object).find(jQuery(document.getElementById('frm_field_'+key+'_container')));
						if($fieldCont.length && $fieldCont.is(':visible')){
							cont_submit=false;
							if(jump === ''){
								frmFrontForm.scrollMsg(key, object);
								jump='#frm_field_'+key+'_container';
							}
                            var $recapcha = jQuery(object).find('#frm_field_'+key+'_container .g-recaptcha');
							if($recapcha.length){
								show_captcha = true;
                                grecaptcha.reset();
							}
							
							$fieldCont.addClass('frm_blank_field');
							if(typeof(frmThemeOverride_frmPlaceError) == 'function'){
								frmThemeOverride_frmPlaceError(key,errObj);
							}else{
								$fieldCont.append('<div class="frm_error">'+errObj[key]+'</div>');
							}
						}else if(key == 'redirect'){
							window.location = errObj[key];
							return;
						}
					}
					if(show_captcha !== true){
						jQuery(object).find('.g-recaptcha').closest('.frm_form_field').replaceWith('<input type="hidden" name="recaptcha_checked" value="'+ frm_js.nonce +'">');
					}
					if(cont_submit){
						object.submit();
					}
				}
			},
			error:function(){
				jQuery(object).find('input[type="submit"], input[type="button"]').removeAttr('disabled');object.submit();
			}
		});
	}

	function clearDefault(){
		/*jshint validthis:true */
		toggleDefault(jQuery(this), 'clear');
	}

	function replaceDefault(){
		/*jshint validthis:true */
		toggleDefault(jQuery(this), 'replace');
	}
	
	function toggleDefault($thisField, e){
		var v = $thisField.data('frmval').replace(/(\n|\r\n)/g, '\r');
		if ( v === '' || typeof v == 'undefined' ) {
			return false;
		}
		var thisVal = $thisField.val().replace(/(\n|\r\n)/g, '\r');
		
		if ( 'replace' == e ) {
			if ( thisVal === '' ) {
				$thisField.addClass('frm_default').val(v);
			}
		} else if ( thisVal == v ) {
			$thisField.removeClass('frm_default').val('');
		}
	}

	function resendEmail(){
		/*jshint validthis:true */
		var $link = jQuery(this);
		var entry_id = $link.data('eid');
		var form_id = $link.data('fid');
		$link.append('<span class="spinner" style="display:inline"></span>');
		jQuery.ajax({
			type:'POST',url:frm_js.ajax_url,
			data:{action:'frm_entries_send_email', entry_id:entry_id, form_id:form_id},
			success:function(msg){
				$link.replaceWith(msg);
			}
		});
		return false;
	}

	/* File Fields */
	function nextUpload(){
		/*jshint validthis:true */
		var obj = jQuery(this);
		var id = obj.data('fid');
		obj.wrap('<div class="frm_file_names frm_uploaded_files">');
		var files = obj.get(0).files;
		for ( var i = 0; i < files.length; i++ ) {
			if ( files.length == 1 ) {
				obj.after(files[i].name+' <a href="#" class="frm_clear_file_link">'+frm_js.remove+'</a>');
			} else {
				obj.after(files[i].name +'<br/>');
			}
		}

        obj.hide();

        var fileName = 'file'+ id;
        var fname = obj.attr('name');
        if ( fname != 'item_meta['+ id +'][]' ) {
            // this is a repeatable field
            var nameParts = fname.replace('item_meta[', '').replace('[]', '').split('][');
            if ( nameParts.length == 3 ) {
                fileName = fileName +'-'+ nameParts[1];
            }
        }

        obj.closest('.frm_form_field').find('.frm_uploaded_files:last').after('<input name="'+ fileName +'[]" data-fid="'+ id +'"class="frm_multiple_file" multiple="multiple" type="file" />');
	}

	function removeDiv(){
		/*jshint validthis:true */
		fadeOut(jQuery(this).parent('.frm_uploaded_files'));
	}
	
	function clearFile(){
		/*jshint validthis:true */
		jQuery(this).parent('.frm_file_names').replaceWith('');
		return false;
	}
	
	/* Repeating Fields */
	function removeRow(){
		/*jshint validthis:true */
		var id = 'frm_section_'+ jQuery(this).data('parent') +'-'+ jQuery(this).data('key');
		fadeOut(jQuery(document.getElementById(id)));
		return false;
	}

	function addRow(){
		/*jshint validthis:true */
		var id = jQuery(this).data('parent');
		var i = 0;
		if ( jQuery('.frm_repeat_'+id).length > 0 ) {
			i = 1 + parseInt(jQuery('.frm_repeat_'+ id +':last').attr('id').replace('frm_section_'+ id +'-', ''));
			if ( typeof i == 'undefined' ) {
				i = 1;
			}
		}

		jQuery.ajax({
			type:'POST',url:frm_js.ajax_url,
			data:'action=frm_add_form_row&field_id='+id+'&i='+i,
			success:function(html){
				var item = jQuery(html).hide().fadeIn('slow');
				jQuery('.frm_repeat_'+ id +':last').after(item);
                var star = jQuery(html).find('.star');
                if ( star.length > 0 ) {
                    // trigger star fields
                    jQuery('.star').rating();
                }

                var autocomplete = jQuery(html).find('.frm_chzn');
				if ( autocomplete.length > 0 && jQuery().chosen ) {
                    // trigger autocomplete
					jQuery('.frm_chzn').chosen({allow_single_deselect:true});
				}
			}
		});


		return false;
	}

	/* General Helpers */
	function fadeOut($remove){
		$remove.fadeOut('slow', function(){
			$remove.remove();
		});
	}
	
	function empty($obj) {
		if ( $obj !== null ) {
			while ( $obj.firstChild ) {
				$obj.removeChild($obj.firstChild);
			}
		}
	}

	function isNumeric( obj ) {
		return !jQuery.isArray( obj ) && (obj - parseFloat( obj ) + 1) >= 0;
	}

	return{
		init: function(){
			frmFrontForm.invisible('.frm_ajax_loading');

			jQuery(document).on('click', '.frm_trigger', toggleSection);
			var $blankField = jQuery('.frm_blank_field');
			if ( $blankField.length ) {
				$blankField.closest('.frm_toggle_container').prev('.frm_trigger').click();
			}

			if ( jQuery.isFunction(jQuery.fn.placeholder) ) {
				jQuery('.frm-show-form input, .frm-show-form textarea').placeholder();
			} else {
				jQuery('.frm-show-form input[onblur], .frm-show-form textarea[onblur]').each(function(){
					if(jQuery(this).val() === '' ){
						jQuery(this).blur();
					}
				});
			}
			
			jQuery(document).on('focus', '.frm_toggle_default', clearDefault);
			jQuery(document).on('blur', '.frm_toggle_default', replaceDefault);
			jQuery('.frm_toggle_default').blur();

			jQuery(document.getElementById('frm_resend_email')).click(resendEmail);

			jQuery(document).on('change', '.frm_multiple_file', nextUpload);
			jQuery(document).on('click', '.frm_clear_file_link', clearFile);
			jQuery(document).on('click', '.frm_remove_link', removeDiv);

			jQuery(document).on('change', '.frm-show-form input[name^="item_meta"], .frm-show-form select[name^="item_meta"], .frm-show-form textarea[name^="item_meta"]', maybeCheckDependent);
			
			jQuery(document).on('click', '.frm-show-form input[type="submit"], .frm-show-form input[name="frm_prev_page"], .frm-show-form .frm_save_draft', setNextPage);
            
            jQuery(document).on('change', '.frm_other_container input[type="checkbox"], .frm_other_container input[type="radio"]', showOtherText);
			
			jQuery(document).on('click', '.frm_remove_form_row', removeRow);
			jQuery(document).on('click', '.frm_add_form_row', addRow);

			// toggle collapsible entries shortcode
			jQuery('.frm_month_heading, .frm_year_heading').toggle(
				function(){
					jQuery(this).children('.ui-icon-triangle-1-e, .ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-s').removeClass('ui-icon-triangle-1-e');
					jQuery(this).next('.frm_toggle_container').fadeIn('slow');
				},
				function(){
					jQuery(this).children('.ui-icon-triangle-1-s, .ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-e').removeClass('ui-icon-triangle-1-s');
					jQuery(this).next('.frm_toggle_container').hide();
				}
			);
		},

		submitForm: function(e){
			e.preventDefault();
			if(jQuery(this).find('.wp-editor-wrap').length && typeof(tinyMCE) != 'undefined'){
				tinyMCE.triggerSave();
			}
			getFormErrors(this);
		},

		scrollMsg: function(id, object){
			var newPos = '';
			if(typeof(object) == 'undefined'){
				newPos = jQuery(document.getElementById('frm_form_'+id+'_container')).offset().top;
			}else{
				newPos = jQuery(object).find(document.getElementById('frm_field_'+id+'_container')).offset().top;
			}

			if(!newPos){
				return;
			}
			newPos = newPos-frm_js.offset;

			var m=jQuery('html').css('margin-top');
			var b=jQuery('body').css('margin-top');
			if(m || b){
				newPos=newPos-parseInt(m)-parseInt(b);
			}

			var cOff = document.documentElement.scrollTop || document.body.scrollTop;
			if(newPos && (!cOff || cOff > newPos)){
				jQuery(window).scrollTop(newPos);
			}
		},

		hideCondFields: function(ids){
			ids = JSON.parse(ids);
			var len = ids.length;
			for ( var i = 0, l = len; i < l; i++ ) {
                var container = document.getElementById('frm_field_'+ ids[i] +'_container');
                if ( container !== null ) {
				    container.style.display = 'none';
                } else {
                    //frm_field_189-1022-0_container
                }
			}
		},

		checkDependent: function(ids){
			ids = JSON.parse(ids);
			var len = ids.length;
			for ( var i = 0, l = len; i < l; i++ ) {
				checkDependentField('und', ids[i]);
			}
		},
		
		/* Time fields */
		removeUsedTimes: function(obj, timeField){
			var e = jQuery(obj).parents('form:first').find('input[name="id"]');
			jQuery.ajax({
				type:'POST',
				url:frm_js.ajax_url,
				dataType:'json',
				data:{
					action:'frm_fields_ajax_time_options',
					time_field:timeField, date_field:obj.id,
					entry_id: (e ? e.val() : ''), date: jQuery(obj).val()
				},
				success:function(opts){
					var $timeField = jQuery(document.getElementById(timeField));
					$timeField.find('option').removeAttr('disabled');
					if(opts && opts !== ''){
						for(var opt in opts){
							$timeField.find('option[value="'+opt+'"]').attr('disabled', 'disabled');
						}
					}
				}
			});
		},
		
		escapeHtml: function(text){
			return text
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		},
		
		invisible: function(classes) {
			jQuery(classes).css('visibility', 'hidden');
		},
		
		visible: function(classes) {
			jQuery(classes).css('visibility', 'visible');
		}
	};
}
var frmFrontForm = frmFrontFormJS();

jQuery(document).ready(function($){
	frmFrontForm.init();
});

function frmEditEntry(entry_id,prefix,post_id,form_id,cancel,hclass){
	var $edit = jQuery(document.getElementById('frm_edit_'+entry_id));
	var label = $edit.html();
	var $cont = jQuery(document.getElementById(prefix+entry_id));
	var orig = $cont.html();
	$cont.html('<span class="frm-loading-img" id="'+prefix+entry_id+'"></span><div class="frm_orig_content" style="display:none">'+orig+'</div>');
	jQuery.ajax({
		type:'POST',url:frm_js.ajax_url,dataType:'html',
		data:{action:'frm_entries_edit_entry_ajax', post_id:post_id, entry_id:entry_id, id:form_id},
		success:function(html){
			$cont.children('.frm-loading-img').replaceWith(html);
			$edit.replaceWith('<span id="frm_edit_'+entry_id+'"><a onclick="frmCancelEdit('+entry_id+',\''+prefix+'\',\''+ frmFrontForm.escapeHtml(label) +'\','+post_id+','+form_id+',\''+hclass+'\')" class="'+hclass+'">'+cancel+'</a></span>');
		}
	});
}

function frmCancelEdit(entry_id,prefix,label,post_id,form_id,hclass){
	var $edit = jQuery(document.getElementById('frm_edit_'+entry_id));
	var $link = $edit.find('a');
	var cancel = $link.html();
	
	if(!$link.hasClass('frm_ajax_edited')){
		var $cont = jQuery(document.getElementById(prefix+entry_id));
		$cont.children('.frm_forms').replaceWith('');
		$cont.children('.frm_orig_content').fadeIn('slow').removeClass('frm_orig_content');
	}
	$edit.replaceWith('<a id="frm_edit_'+entry_id+'" class="frm_edit_link '+hclass+'" href="javascript:frmEditEntry('+entry_id+',\''+prefix+'\','+post_id+','+form_id+',\''+ frmFrontForm.escapeHtml(cancel) +'\',\''+hclass+'\')">'+label+'</a>');
}

function frmUpdateField(entry_id,field_id,value,message,num){
	jQuery(document.getElementById('frm_update_field_'+entry_id+'_'+field_id)).html('<span class="frm-loading-img"></span>');
	jQuery.ajax({
		type:'POST',url:frm_js.ajax_url,
		data:{action:'frm_entries_update_field_ajax', entry_id:entry_id, field_id:field_id, value:value},
		success:function(){
			if(message.replace(/^\s+|\s+$/g,'') === ''){
				jQuery(document.getElementById('frm_update_field_'+entry_id+'_'+field_id+'_'+num)).fadeOut('slow');
			}else{
				jQuery(document.getElementById('frm_update_field_'+entry_id+'_'+field_id+'_'+num)).replaceWith(message);
			}
		}
	});
}

function frmDeleteEntry(entry_id,prefix){	
	jQuery(document.getElementById('frm_delete_'+entry_id)).replaceWith('<span class="frm-loading-img" id="frm_delete_'+entry_id+'"></span>');
	jQuery.ajax({
		type:'POST',url:frm_js.ajax_url,
		data:{action:'frm_entries_destroy', entry:entry_id},
		success:function(html){
			if(html.replace(/^\s+|\s+$/g,'') == 'success')
				jQuery(document.getElementById(prefix+entry_id)).fadeOut('slow');
			else
				jQuery(document.getElementById('frm_delete_'+entry_id)).replaceWith(html);
			
		}
	});
}

function frmOnSubmit(e){
	console.warn('DEPRECATED: function frmOnSubmit in v2.0 use frmFrontForm.submitForm'); 
	frmFrontForm.submitForm(e, this);
}

function frm_resend_email(entry_id,form_id){
	console.warn('DEPRECATED: function frm_resend_email in v2.0'); 
	$link = jQuery(document.getElementById('frm_resend_email'));
	$link.append('<span class="spinner" style="display:inline"></span>');
	jQuery.ajax({
		type:'POST',url:frm_js.ajax_url,
		data:{action:'frm_entries_send_email', entry_id:entry_id, form_id:form_id},
		success:function(msg){
			$link.replaceWith(msg);
		}
	});
}


