<!DOCTYPE html>
<html>
<head>
    <title>Resumen del procesamiento de comprobantes</title>
</head>
<body>
    <h1>Estimado {{ $user->name }},</h1>
    <p>A continuación, encontrarás un resumen del procesamiento de tus comprobantes:</p>

    <h2>Comprobantes Exitosos</h2>
    @if (count($successfulVouchers) > 0)
        @foreach ($successfulVouchers as $voucher)
            <ul>
                <li>Nombre del Emisor: {{ $voucher->issuer_name }}</li>
                <li>Tipo de Documento del Emisor: {{ $voucher->issuer_document_type }}</li>
                <li>Número de Documento del Emisor: {{ $voucher->issuer_document_number }}</li>
                <li>Nombre del Receptor: {{ $voucher->receiver_name }}</li>
                <li>Tipo de Documento del Receptor: {{ $voucher->receiver_document_type }}</li>
                <li>Número de Documento del Receptor: {{ $voucher->receiver_document_number }}</li>
                <li>Monto Total: {{ $voucher->total_amount }}</li>
            </ul>
        @endforeach
    @else
        <p>No se registraron comprobantes exitosos.</p>
    @endif

    <h2>Comprobantes Fallidos</h2>
    @if (count($failedVouchers) > 0)
        <ul>
            @foreach ($failedVouchers as $failed)
                <li>
                    <strong>Error:</strong> {{ $failed['error'] }} <br>
                    <strong>Contenido del XML:</strong> {{ $failed['xml'] }}
                </li>
            @endforeach
        </ul>
    @else
        <p>No hubo comprobantes fallidos.</p>
    @endif

    <p>Gracias por usar nuestro servicio.</p>
</body>
</html>