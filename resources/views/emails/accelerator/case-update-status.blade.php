<div>
    <h2>Ваш кейс изменил статус</h2>
    <p>
        Текущий статус: {{ $case->status->name }} <br>
        @php($messages = $case->getMessages())
        @if(count($messages) > 0)
            Комментарий: {{ $messages[0]['message'] }}
        @endif
    </p>
</div>
