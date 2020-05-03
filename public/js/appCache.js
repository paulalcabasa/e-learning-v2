class AppCache
{
    saveItem(name, data, is_object = false) {
        return localStorage.setItem(name, is_object ? JSON.stringify(data) : data);
    }

    getItem(name) {
        return localStorage.getItem(name);
    }

    deleteItem(name) {
        localStorage.removeItem(name);
        var check = this.getItem(name)
        return check == null ? true : false;
    }
}