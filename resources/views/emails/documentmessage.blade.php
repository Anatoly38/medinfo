Пользователь {{ $worker->description }} оставил сообщение:
<p> {{ $doc_message->message }}</p>
<p>Для документа <a href="{{ config('app.url') }}/datainput/formdashboard_v2/{{ $document->id }}">№{{ $document->id }}</a> по форме №{{ $document->form->form_code }}</p>
<p>Учреждение: {{ $document->unit->unit_name }}</p>
<p class="text-info">Это письмо создано автоматической системой рассылки при смене статуса отчетного документа. Не отвечайте на него!</p>
<p class="text-info">Для создания сообщения необходимо авторизоваться на портале <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>,
    выбрать нужный документ, нажать кнопку на панели инструментов "Создать сообщение/комментарий к документу".</p>