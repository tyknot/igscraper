const btn = document.querySelector('#user-submit');
const userInput = document.getElementById('user-input');
let myForm = document.getElementById('search-user-form');

/*const tryt = () =>{
    console.log(userInput.value)
}*/

myForm.addEventListener('submit', async function (e){
    e.preventDefault();
    let form_data = new FormData(this);
    let headers = 'Content-Type":"application/x-form-urlencoded';

    //form_data.append();

    await fetch('ig.php', {
        method: "POST",
        body: form_data,
    }).then(response => {
        return response;
    }).then(html => {
        //let text = document.querySelector('.ig-tool').innerHTML = text;
        const htmlRes = await html.text();
        console.log(htmlRes);
    }).catch(error => console.log(error))
});

//btn.addEventListener('click', load)


/*function load(e){
    let form_data = new FormData();
    let xhr = new XMLHttpRequest();
    e.preventDefault();
    /*for(let value of form_data.values()){        
    }
    form_data.append(userInput.name, userInput.value);
    console.log(form_data.values());
    let returnValue = xhr.onreadystatechange = function(){
        if (xhr.readyState === 4) {
            // Check the response status.
            if (xhr.status === 200) {
                console.log(document.querySelector('.ig-tool').innerHTMl = xhr.responseText);
                //console.log(document.querySelector('#user-input').value);
            } else {
                console.log('doesnt work');
            }
        }
    }
    xhr.open('POST', 'ig.php');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlendcoded');
    xhr.send(form_data.values());
    return returnValue;
}

myForm.addEventListener('submit', load);*/


