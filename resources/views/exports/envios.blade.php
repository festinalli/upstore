<table>
    <thead>
        <tr>
            <th><b>Código</b></th>
            <th><b>Cliente</b></th>
            <th><b>Local atual</b></th>
            <th><b>Status</b></th>
            <th><b>Tipo</b></th>
            <th><b>Rastreamento</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($envios as $envio)
            <tr>
                <td>{{ $envio->id }}</td>
                <td>{{ $envio->servico ? $envio->servico->cliente->nome : '' }}</td>
                <td>Local enviado pelo correio</td>
                <td>{{ $envio->status }}</td>
                <td>
                    @if($envio->servico->tipo == 'M') Manutenção
                    @elseif($envio->servico->tipo == 'V') Venda seu usado
                    @elseif($envio->servico->tipo == 'T') Aparelho como Entrada
                    @endif
                </td>
                <td>{{ $envio->servico->etiqueta_id }}</td>
            </tr>
        @endforeach
    </tbody>
</table>