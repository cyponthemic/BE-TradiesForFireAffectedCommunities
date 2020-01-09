<template>
    <div>
        <form @submit.prevent="submit">
            <input ref="file" type="file" />
            <button type="submit">Upload</button>
        </form>

        <hr />

        <h1>Tradies {{ geoTradies.length }}</h1>
        <h1>Tradies Errors {{ geoTradiesErrors.length }}</h1>

        <button @click="exportData" type="submit">Export</button>
        <ul>
            <li v-for="tradie in  geoTradiesErrors">{{ tradie }}</li>
        </ul>
    </div>
</template>
<script>
import Papa from 'papaparse'

import keyBy from 'lodash-es/keyBy'
import { trim } from 'lodash-es'
export default {
    data() {
        return {
            data: [],
            postcodes: []
        }
    },
    computed: {
        geoTradies() {
            return this.data.map((tradie) => {
                tradie._geoloc = this.postcodes[trim(tradie.Postcode)] || {}
                return tradie
            })
        },
        geoTradiesErrors() {
            return this.geoTradies
                .filter((tradie) => !tradie._geoloc.lat)
                .map((tradie) => tradie.Postcode)
        }
    },
    mounted() {},
    methods: {
        exportData() {
          axios.post('/api/export', {
              tradies: this.geoTradies
          })
        },
        submit(e) {
            Papa.parse(this.$refs.file.files[0], {
                header: true,
                complete: (e) => {
                    this.data = e.data
                }
            })

            Papa.parse('/csv/Australian_Post_Codes_Lat_Lon.csv', {
                header: true,
                download: true,
                complete: (e) => {
                    this.postcodes = keyBy(e.data, 'postcode')
                }
            })
        }
    }
}
</script>
