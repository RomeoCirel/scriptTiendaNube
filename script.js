document.addEventListener('DOMContentLoaded', function() {
    if (LS){
        void postData();
    }
});

const postData = async () => {
    const id = document.cookie.split('; ')
        .find(row => row.startsWith('catch_data_id='))?.split('=')[1] ?? null;
    const response = await fetch('/catch-data', {
        method: 'POST',
        body: JSON.stringify({
            id: id,
            LS: {...LS}
        }),
        headers: {
            'Content-type': 'application/json; charset=UTF-8',
        },
    });
    if (response.status === 200) {
        const data = await response.json();
        if (data.id && !document.cookie.split('; ')
            .find(row => row.startsWith('catch_data_id='))) {
            document.cookie = "catch_data_id=" + data.id;
        }
    }
}