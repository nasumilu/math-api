
export default class {

    async calculate(mathml) {

        const response = await fetch('math', {
            method: 'POST',
            mode: 'cors',
            headers: {
                'Content-Type': 'text/xml; charset=utf-8'
            },
            body: mathml.outerHTML
        });

        const xml = await response.text();
        return new window.DOMParser().parseFromString(xml, 'text/xml');
    }

}
