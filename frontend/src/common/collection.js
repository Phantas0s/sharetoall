import _ from 'lodash/object';

class Collection {

    constructor(values) {
        if (values) {
            this.setValues(values);
        }
    }

    setValues(values) {
        _.forOwn(values, (value, key) => this[key] = value);

        return this;
    }

    getValues() {
        const result = {};
        _.forOwn(this, (value, key) => result[key] = value);

        return result;
    }
}

export default Collection;
