document
    .getElementById('upload')
    .addEventListener('change', 
    function (event) {
        for (let i = 0; i < event.target.files.length; i++) {
            let file = event.target.files[i];     
            const reader = new FileReader();

            reader.onload = function (event) {
                try {
                    const fileReader = event.target;
                    const activity = FileHandler.fileToActivity(fileReader.result, file.name);
                    
                    // check for dubs
                    if ();
                    
                    //upload to db
                    
                }
                catch (error) {
                    fileErrorHandler(error);
                }               
            }

            reader.readAsText(file);
        };
    }
);