
const success = ({ data }, resolve) => {
    //resolve(console.log(data));
}

const failed = ({ error }, reject) => {
    //reject(console.log(error));
};

export default (form, data) => {
    new Promise((resolve, reject) => {
        form.post('/api/recep-capability', data).then((response) => {
            success(response, resolve);
        }).catch((error) => {
            failed(error, reject);
        });
    });
};
