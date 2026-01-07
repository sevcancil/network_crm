$(document).ready(function(){
    // Basit Adres Verisi (Bunu genişletebilirsin)
    const locationData = {
        "Turkiye": {
            "İstanbul": ["Kadıköy", "Beşiktaş", "Şişli", "Ümraniye"],
            "Ankara": ["Çankaya", "Keçiören", "Mamak"],
            "İzmir": ["Konak", "Karşıyaka", "Bornova"]
        },
        "Almanya": {
            "Berlin": ["Mitte", "Pankow"],
            "Münih": ["Allach", "Altstadt"]
        }
    };

    // Ülke Seçimi
    $('#country').change(function(){
        let country = $(this).val();
        let citySelect = $('#city');
        let districtSelect = $('#district');
        citySelect.empty().append('<option value="">İl Seçiniz</option>');
        districtSelect.empty().append('<option value="">İlçe Seçiniz</option>');
        
        if(locationData[country]) {
            $.each(locationData[country], function(city, districts){
                citySelect.append(new Option(city, city));
            });
        }
    });

    // İl Seçimi
    $('#city').change(function(){
        let country = $('#country').val();
        let city = $(this).val();
        let districtSelect = $('#district');
        districtSelect.empty().append('<option value="">İlçe Seçiniz</option>');
        
        if(locationData[country] && locationData[country][city]) {
            locationData[country][city].forEach(function(dist){
                districtSelect.append(new Option(dist, dist));
            });
        }
    });

    // Edit Modu İçin Otomatik Seçim (Veri varsa)
    if(typeof selectedCountry !== 'undefined' && selectedCountry) {
        $('#country').val(selectedCountry).trigger('change');
        if(typeof selectedCity !== 'undefined' && selectedCity) {
            $('#city').val(selectedCity).trigger('change');
            if(typeof selectedDistrict !== 'undefined' && selectedDistrict) {
                $('#district').val(selectedDistrict);
            }
        }
    }

    // Canlı Arama
    $("#live_search").keyup(function(){
        var input = $(this).val();
        $.ajax({
            url: "../api/search.php",
            method: "POST",
            data: {query: input},
            success: function(data){
                $("#search_results").html(data);
            }
        });
    });
});