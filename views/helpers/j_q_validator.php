<?php
/*
This file is part of CakePHP JQValidator Plugin.
 
CakePHP JQValidator Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
 
CakePHP JQValidator Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with CakePHP JQValidator Plugin. If not, see <http://www.gnu.org/licenses/>.
*/

class JQValidatorHelper extends AppHelper
{
	/*
	 * Returns a script which creates the client side - JQValidator object,
	 * and a script which inits the form validation logic.
	 */
	function validator()
	{
		// JQValidator javascript object creation script
		$script = "
		<script type=\"text/javascript\">

			$(document).ready
			(
				function()
				{
					if(window.JQValidator == undefined)
					{
						window.JQValidator = { 'name' : 'JQValidator', 'forms' : new Array() };

						window.JQValidator.initFormValidator = function (formValidationData)
						{
							$(formValidationData.formId).submit
							(
								function ()
								{
									return window.JQValidator.validateForm(formValidationData);
								}
							);
						};

						window.JQValidator.validateForm = function (formValidationData)
						{
							var failed = new Array();
							$.each
							(
								formValidationData.rules,
								function (elementId, elementRules)
								{
									switch(elementRules.rule)
									{
										case 'notempty':
											value = $(formValidationData.formId + ' ' + elementId).val();
											regEx = new RegExp('^.+$');
											if(!regEx.test(value))
												failed.push({ 'control' : $(formValidationData.formId + ' ' + elementId), 'message' : elementRules.message });
											break;

										case 'numeric':
											value = $(formValidationData.formId + ' ' + elementId).val();
											regEx = new RegExp('^[0-9]+$');
											if(!regEx.test(value))
												failed.push({ 'control' : $(formValidationData.formId + ' ' + elementId), 'message' : elementRules.message });
											break;

										case 'date':
											value =
												$(formValidationData.formId + ' ' + elementId.replace(']\"]', '][day]\"]')).val() + '/' +
												$(formValidationData.formId + ' ' + elementId.replace(']\"]', '][month]\"]')).val() + '/' +
												$(formValidationData.formId + ' ' + elementId.replace(']\"]', '][year]\"]')).val();
											regEx = new RegExp('^(((0[1-9]|[12]\\\\d|3[01])\\\\/(0[13578]|1[02])\\\\/((1[6-9]|[2-9]\\\\d)\\\\d{2}))|((0[1-9]|[12]\\\\d|30)\\\\/(0[13456789]|1[012])\\\\/((1[6-9]|[2-9]\\\\d)\\\\d{2}))|((0[1-9]|1\\\\d|2[0-8])\\\\/02\\\\/((1[6-9]|[2-9]\\\\d)\\\\d{2}))|(29\\\\/02\\\\/((1[6-9]|[2-9]\\\\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$');
											if(!regEx.test(value))
												failed.push({ 'control' : $(formValidationData.formId + ' ' + elementId.replace(']\"]', '][day]\"]') + ', ' + formValidationData.formId + ' ' + elementId.replace(']\"]', '][month]\"]') + ', ' + formValidationData.formId + ' ' + elementId.replace(']\"]', '][year]\"]')), 'message' : elementRules.message });
											break;
									}
								}
							);

							if(failed.length > 0)
							{
								window.JQValidator.displayErrorMessage(formValidationData.errorMessageTitle, failed);
								return false;
							}

							return true;
						};

						window.JQValidator.displayErrorMessage = function (errorMessageTitle, failed)
						{
							var focused = false;
							var message = errorMessageTitle;

							$.each
							(
								failed,
								function (index, data)
								{
									message += '\\n - ' + data.message;

									if(!focused)
									{
										focused = true;
										data.control.first().focus().select();
									}
								}
							);

							alert(message);
						};
					}
				}
			);

		</script>
		";

		// get the validation rules saved in the component
		$validations = Configure::read('JQValidator.jQValidations');

		// JQValidator forms validation init script
		$script .= "
		<script type=\"text/javascript\">
			$(document).ready
			(
				function()
				{
		";

		foreach($validations as $validation)
		{
			$modelName = $validation['modelName'];
			$validationOptions = $validation['validationOptions'];
			$errorMessageTitle = $validation['errorMessageTitle'];
			$formId = $validation['formId'];

			$script .= "
					rules = {";

			foreach($validationOptions as $name => $settings)
			{
				foreach($settings as $type => $options)
				{
					if(isset($options['message']))
					{
						$message = $options['message'];
						$script .= "'[name=\"data[$modelName][$name]\"]':";
						$script .= "{'rule':'$type','message':'$message'},";
					}
					break;
				}
			}

			$script .= "};
					window.JQValidator.forms['$formId'] = { 'formId': '$formId', 'rules': rules, 'errorMessageTitle': '$errorMessageTitle' };
					window.JQValidator.initFormValidator(window.JQValidator.forms['$formId']);
					";
		}
		$script .= "
				}
			);
		</script>";

		return $script;
	}

}
