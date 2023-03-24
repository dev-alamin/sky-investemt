<?php

// check if form was submitted
if(isset($_POST['register'])){

    // get file info
    $file_name = $_FILES['pdf_file']['name'];
    $file_size = $_FILES['pdf_file']['size'];
    $file_tmp = $_FILES['pdf_file']['tmp_name'];
    $file_type = $_FILES['pdf_file']['type'];
    $file_ext = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));

    // set allowed file extensions
    $extensions = array("pdf");

    if(in_array($file_ext,$extensions) === false){
        // if file extension not allowed, show error message
        echo "Extension not allowed, please choose a PDF file.";
    } else {
        // if file extension allowed, upload file to WordPress media library
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'] . '/' . $file_name;
        $uploaded = move_uploaded_file($file_tmp, $upload_path);

        if($uploaded){
            // if file uploaded successfully, create attachment post
            $attachment = array(
                'post_title' => $file_name,
                'post_mime_type' => $file_type,
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $upload_path );

            // set attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_path );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // get attachment URL
            $attachment_url = wp_get_attachment_url( $attach_id );

            // show success message and link to uploaded file
            echo "File uploaded successfully. Here's the link to the file: <a href='$attachment_url'>$file_name</a>";
        } else {
            // if file upload failed, show error message
            echo "File upload failed.";
        }
    }
}
