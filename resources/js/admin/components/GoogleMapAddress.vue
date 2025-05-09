<template>
    <div>
      <input
        ref="autocompleteInput"
        type="text"
        placeholder="Enter your address"
        class="address-input"
      />
      <div v-if="address">
        <p><strong>Address:</strong> {{ address }}</p>
        <p><strong>Latitude:</strong> {{ lat }}</p>
        <p><strong>Longitude:</strong> {{ lng }}</p>
        <p><strong>Postal Code:</strong> {{ postalCode }}</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        address: '',
        lat: null,
        lng: null,
        postalCode: '',
        autocomplete: null,
      };
    },
    mounted() {
      // Wait until Google Maps is loaded
      if (window.google && window.google.maps) {
        this.initAutocomplete();
      } else {
        const check = setInterval(() => {
          if (window.google && window.google.maps) {
            clearInterval(check);
            this.initAutocomplete();
          }
        }, 200);
      }
    },
    methods: {
      initAutocomplete() {
        const input = this.$refs.autocompleteInput;
        this.autocomplete = new google.maps.places.Autocomplete(input, {
          types: ['geocode'],
        });
  
        this.autocomplete.addListener('place_changed', () => {
          const place = this.autocomplete.getPlace();
  console.log(place);
          this.address = place.formatted_address;
          this.lat = place.geometry.location.lat();
          this.lng = place.geometry.location.lng();
  
          const postalComp = place.address_components.find(comp =>
            comp.types.includes('postal_code')
          );
          this.postalCode = postalComp ? postalComp.long_name : '';
        });
      },
    },
  };
  </script>
  
  <style scoped>
  .address-input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
  }
  </style>
  


  ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIO+eQBX/U5yK+glc8uzW1IVzptDF+qwMNnAskcAeKolE chadnitalukder2@gmail.com
