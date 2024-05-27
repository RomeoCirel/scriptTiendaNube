document.addEventListener('DOMContentLoaded', function () {
    if (LS) {
        void postData();
    }
});

const COOKIE_NAME = 'catch_data_id=';

const getCookieValue = (name) => {
    return document.cookie.split('; ')
        .find(row => row.startsWith(name))?.split('=')[1] ?? null;
}

const setCookieValue = (name, value) => {
    document.cookie = name + value;
}

async function getIsIncognito() {
    let isIncognito = false;

    await detectIncognito().then((result) => {
        isIncognito = result.isPrivate;
    })

    return isIncognito;
}

const postData = async () => {
    const isIncognito = await getIsIncognito();
    const id = getCookieValue(COOKIE_NAME);

    try {
        const response = await fetch('/catch-data', {
            method: 'POST',
            body: JSON.stringify({
                id: id,
                LS: {...LS},
                isIncognito: isIncognito,
            }),
            headers: {
                'Content-type': 'application/json; charset=UTF-8',
            },
        });

        console.log(response)

        if (response.status === 200) {
            const data = await response.json();
            if (data.id && !getCookieValue(COOKIE_NAME)) {
                setCookieValue(COOKIE_NAME, data.id);
            }
        }

    } catch (error) {
        console.error('Error:', error);
    }
}