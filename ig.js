const btn = document.querySelector('#user-submit');
const userInput = document.getElementById('user-input');
const myForm = document.getElementById('search-user-form');
const igHtml = document.querySelector('.ig-tool');

function searchUserProfile(){
    let form_data = new FormData(myForm);
    const userAgent = ['Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
                    'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:10.0) Gecko/20100101 Firefox/10.0',
                    'Mozilla/5.0 (X11; Linux i686; rv:10.0) Gecko/20100101 Firefox/10.0']
    const randAgent = userAgent[Math.floor(Math.random() * userAgent.length)]

    return axios.post('ig.php', form_data, {
    }).then((response)=>{
        const html = igHtml.innerHTML = response.data
        console.log(response)
    }).catch(error=>{console.log('Error in request')})

}

myForm.addEventListener('submit', async (e)=>{
    e.preventDefault()
    await searchUserProfile()
    
});
