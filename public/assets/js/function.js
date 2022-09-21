const handleAddToCart = (ref_id) => {
    const divColors = document.getElementById('display-colors');
        document.getElementById('js-select-size').addEventListener('change', e => {
            divColors.innerHTML = '';
            axios.post('/api/reference/size/colors', { ref_id : ref_id, size_id : e.target.value })
            .then(response => response.data)
            .then(data => {

                const select = document.createElement('select');
                select.classList.add("form-select");
                select.name = 'color';
                select.id = "js-select-color"
                
                const defaultOpt = document.createElement('option');
                defaultOpt.innerHTML = 'Selectionner ue couleur';
                
                select.appendChild(defaultOpt);
                divColors.appendChild(select);

                const colors = Object.values(data.colors);
                const idColors = Object.keys(data.colors);
                console.log(colors, idColors);

                for (let i = 0; i < colors.length; i++) {
                    const option = document.createElement('option');
                    option.value = idColors[i];
                    option.innerHTML = colors[i];
                    select.appendChild(option);
                }

                document.getElementById('js-select-color').addEventListener('change', e => {
                    console.log(e.target.value);
                    document.getElementById('js-add-to-cart').classList.remove('d-none');
                })
            })
        });

}