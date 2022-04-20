/* confirm delete order */
// .delete
let deleteHandler = document.querySelectorAll('.delete');
if (deleteHandler){
    for(let i=0;i<deleteHandler.length;i++){
        deleteHandler[i].addEventListener('click', function (e) {
            if (!confirm('Confirm action!')) {
                e.preventDefault();
            }
        });
    }
}
// .deletebd
let deletebdHandler = document.querySelectorAll('.deletebd');
if (deletebdHandler){
    for(let i=0;i<deletebdHandler.length;i++){
        deletebdHandler[i].addEventListener('click', function (e) {
            if (!confirm('Are you sure? This action is cannot revert!')) {
                e.preventDefault();
            }
        });
    }
}

/* order edit ---  */
// .redact
let redactHandler = document.querySelector('.redact');
if (redactHandler){
    redactHandler.addEventListener('click', function (e) {
        if (confirm('You may change only comment!')) {

        }

        return false;
    });
}

