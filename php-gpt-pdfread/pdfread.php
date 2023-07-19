<?php
function pdf_to_text( $filename ) {
    $temp_file = tempnam( "/tmp", "PDFTOTEXT" );
    exec( "pdftotext " . escapeshellarg( $filename ) . " " . escapeshellarg( $temp_file ) );

    register_shutdown_function(function() use ( $temp_file ) {
        @unlink($temp_file);
    });

    return $temp_file;
}

function chunk_text_file( $filename, $chunk_size = 4000, $overlap = 1000 ) {
    if( $overlap > $chunk_size ) {
        throw new \Exception( "Overlap must be smaller than chunk size" );
    }

    $chunks = [];

    $file = fopen( $filename, "r" );
    while( ! feof( $file ) ) {
        $chunk = fread( $file, $chunk_size );
        $chunks[] = $chunk;
        if( feof( $file ) ) {
            break;
        }
        fseek( $file, -$overlap, SEEK_CUR );
    }

    return $chunks;
}
