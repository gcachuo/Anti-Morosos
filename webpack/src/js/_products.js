Project.Products = {};

Project.Products.fetch=function () {
    Project.request('products', 'fetch').done(result => {
        const products = result.response.products;

        $.each(products, function (i, product) {
            $("#selectProductos").append(`<option value="${product.id}">${product.name}</option>`);
        });

        $("#selectProductos").select2({
            placeholder: "Productos y Servicios",
            width: '100%',
            tags: true
        });
    });
};