// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp = new Object(); }

hp.tests = {
	create: {
		init: function()
		{
			try{
				document.getElementById('tests_create_add_question').onclick = function()
				{
					hp.tests.create.add_question();
				}
			}catch(e){ }
		},
		question_index: 0,
		answer_index: new Array(),
		removed_questions: new Array(),
		removed_answers: new Array(),
		add_question: function()
		{
			document.getElementById('tests_create_save').style.opacity = '1';
			document.getElementById('tests_create_save').style.filter = 'alpha(100)';
			document.getElementById('tests_create_save').onclick = function(){ hp.tests.create.save(); }
			
			
			var action_div = document.getElementById('tests_create_actions');
			var question_div = document.createElement('DIV');
			
			var select_box = '<select id="tests_create_answer_type_' + this.question_index + '">';
			select_box += '<option value="single_answer">ett svar</option>';
			select_box += '<option value="multiple_answers">flera svar</option>';
			select_box += '</select>';
			question_div.innerHTML  = '<div class="label">Fråga:</div> <input type="text" id="tests_create_question_' + this.question_index + '">';
			question_div.innerHTML += '<div class="answer_type_label">Låt användare svara med</div>' + select_box + '';
			question_div.innerHTML += ' <button class="button_20" id="tests_create_remove_question_' + this.question_index + '">X</>';
			question_div.innerHTML += '<div id="tests_create_answer_div_' + this.question_index + '"></div>';
			question_div.innerHTML += '<button id="tests_create_add_answer_' + this.question_index + '" class="button_100" style="float: right">Lägg till svar...</button><br />';
			question_div.innerHTML += '<br style="clear: both" />';
			
			question_div.className='tests_create_questiondiv';
			question_div.id = 'tests_create_question_div_' + this.question_index;
			
			action_div.appendChild(question_div);
			
			document.getElementById('tests_create_add_answer_' + this.question_index).onclick = function()
			{
				hp.tests.create.add_answer(this.id.substring(24));
			}
			
			document.getElementById('tests_create_remove_question_' + this.question_index).onclick = function()
			{
				hp.tests.create.remove_question(this.id.substring(29));
			}			
			
			this.answer_index[this.question_index] = 0;
			this.removed_answers[this.question_index] = new Array();
			this.add_answer(this.question_index);

			this.question_index++;
		},
		remove_question: function(question_id)
		{
			this.removed_questions[question_id] = true;
			document.getElementById('tests_create_question_div_' + question_id).style.display = 'none';
		},
		
		add_answer: function(question_id)
		{
			var answer_div = document.getElementById('tests_create_answer_div_' + question_id);
			var answer_div_item = document.createElement('DIV');
			
			answer_div_item.innerHTML = '<div class="label">Svar:</div><input type="text" id="tests_create_answer_' + question_id + '_' + this.answer_index[question_id] + '" /><div class="score_label">Poäng:</div>' + this.new_score_chooser(question_id, this.answer_index[question_id]) + ' <button class="button_20" id="tests_create_remove_answer_' + question_id + '_' + this.answer_index[question_id] + '">X</button>';

			answer_div_item.id = 'tests_create_answer_div_' + question_id + '_' + this.answer_index[question_id];
			answer_div.appendChild(answer_div_item);
			
			document.getElementById('tests_create_remove_answer_' + question_id + '_' + this.answer_index[question_id]).onclick = function()
			{
				hp.tests.create.remove_answer(this.id.substring(27).split('_')[0], this.id.substring(27).split('_')[1]);
			}
			
			this.answer_index[question_id]++;
		},
		remove_answer: function(question_id, answer_id)
		{
			this.removed_answers[question_id][answer_id] = true;
			document.getElementById('tests_create_answer_div_' + question_id + '_' + answer_id).style.display = 'none';
		},
		
		new_score_chooser: function(question_id, answer_id)
		{
			var output = '<select id="tests_create_answer_score_' + question_id + '_' + answer_id + '">';
			for(var score = 0; score <= 10; score++)
			{
				output += '<option value="' + score + '" ' + ((score == 0) ? 'selected="selected"' : '') + '>' + score + 'p</option>';
			}
			output += '</select>';
			return output;
		},
		
		save: function()
		{
			var questions_to_save = new Array();
			
			for(var question = 0; question < this.question_index; question++)
			{
				if(typeof(this.removed_questions[question]) == 'undefined' && document.getElementById('tests_create_question_' + question).value != '')
				{
					var answers = new Array();
					for(var answer = 0; answer < this.answer_index[question]; answer++)
					{
						if(typeof(this.removed_answers[question][answer]) == 'undefined' && document.getElementById('tests_create_answer_' + question + '_' + answer).value != '')
						{
							answers[answers.length] = answer;
						}
					}
					
					if(answers.length > 0)
					{
						question_saver = new Array();
						question_saver['question'] = question;
						question_saver['answers'] = answers;
						questions_to_save[questions_to_save.length] = question_saver;
					}
				}
			}
			
			if(questions_to_save.length > 0)
			{
				if(document.getElementById('tests_create_title').value != '')
				{
					var save_url = '';///tests/save.html';
					save_url += 'title=' + encodeURIComponent(document.getElementById('tests_create_title').value);
					save_url += '&description=' + encodeURIComponent(document.getElementById('tests_create_description').value);
					save_url += '&questions_length=' + questions_to_save.length;
					for(var question_to_save = 0; question_to_save < questions_to_save.length; question_to_save++)
					{
						save_url += '&question_' + question_to_save + '_label=' + encodeURIComponent(document.getElementById('tests_create_question_' + questions_to_save[question_to_save]['question']).value);
						var answer_type = document.getElementById('tests_create_answer_type_' + questions_to_save[question_to_save]['question']);
						save_url += '&question_' + question_to_save + '_answer_type=' + answer_type.options[answer_type.selectedIndex].value;
						save_url += '&question_' + question_to_save + '_answer_length=' + questions_to_save[question_to_save]['answers'].length;
						for(var answer = 0; answer < questions_to_save[question_to_save]['answers'].length; answer++)
						{
							save_url += '&question_' + question_to_save + '_answer_' + answer + '_answer=' + encodeURIComponent(document.getElementById('tests_create_answer_' + questions_to_save[question_to_save]['question'] + '_' + questions_to_save[question_to_save]['answers'][answer]).value);
							var score_chooser = document.getElementById('tests_create_answer_score_' + questions_to_save[question_to_save]['question'] + '_' + questions_to_save[question_to_save]['answers'][answer]);
							save_url += '&question_' + question_to_save + '_answer_' + answer + '_score=' + score_chooser.options[score_chooser.selectedIndex].value;
						}
					}
					
					document.getElementById('tests_create_save').onclick = function(){ alert('Du behöver bara klicka en gång. ;)'); }
					
					var loader = hp.give_me_an_AJAX();
					loader.onreadystatechange = function(){
						if(loader.readyState == 4 && loader.status == 200)
						{
							if(loader.responseText.substring(0, 8) == 'success:')
							{
								alert(loader.responseText.substring(8));
								window.location.href = '/tests/index.php';
							}
							else
							{
								alert('Fel: \n' + loader.responseText);
								alert('Försök om en kvart igen...');
								document.getElementById('tests_create_save').onclick = function(){ hp.tests.create.save(); }
							}
						}
					}
					loader.open('POST', '/tests/save.html', true);
					loader.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8');
					loader.send(save_url);
					
				}
				else
				{
					alert('Du måste hitta på ett namn på din undersökning!');
				}
			}
			else
			{
				alert('Du måste ha med minst en fråga med minst ett svar i!');
			}
			
		}
	}
}

womAdd('hp.tests.create.init()');