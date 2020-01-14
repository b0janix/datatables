<template>
    <div class="card">
        <div class="card-header">{{tableName}}</div>
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="filter">Quick search current results</label>
                    <input type="text" id="filter" class="form-control" v-model="quickSearchQuery">
                </div>
                <div class="form-group col-md-2">
                    <label for="limit">Display records:</label>
                    <select name="" id="limit" class="form-control" v-model="limit" @change="getRecords">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="">All</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th v-for="column in this.displayable">
                            <span class="sortable" @click="sortBy(column)">{{column}}</span>
                            <div class="arrow" v-if="column === sort.key" :class="{'arrow--asc':'asc'===sort.order,'arrow--desc':'desc' === sort.order}"></div>
                        </th>
                        <th>&nbsp</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="record in filteredRecords">
                                <td v-for="(collumnValue, column) in record" @dblclick="edit(column, record.id)">
                                    <template v-if="column === editing.column && record.id === editing.id">
                                        <div class="form-group" :class="{'has-error':editing.error.length > 0}">
                                            <input type="text" class="form-control" :value="collumnValue" @input="debounce($event, update)">
                                            <span v-if="editing.error.length>0">{{editing.error}}</span>
                                        </div>
                                    </template>
                                    <template v-else>
                                        {{collumnValue}}
                                    </template>
                                </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <input class="form-control foot-search" id="id" type="text" placeholder="Search ID" aria-label="Search" @input="debounce($event, search)">
                            </td>
                            <td>
                                <input class="form-control foot-search" id="name" type="text" placeholder="Search Name" aria-label="Search" @input="debounce($event, search)">
                            </td>
                            <td>
                                <input class="form-control foot-search" id="email" type="text" placeholder="Search Email" aria-label="Search" @input="debounce($event, search)">
                            </td>
                            <td>
                                <input class="form-control foot-search" id="created_at" type="text" placeholder="Search Date" aria-label="Search" @input="debounce($event, search)">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['endpoint'],
        data(){
          return {
              displayable: [],
              records: [],
              tableName: {},
              sort: {
                  key: 'id',
                  order: 'asc'
              },
              quickSearchQuery:"",
              limit: 20,
              editing: {
                  error: '',
                  id: null,
                  column:"",
              },
              timeoutId: '',
              search_col_vals:{}
          }
        },
        mounted() {
            this.getRecords();
        },
        computed:{
            filteredRecords()
            {
                let data = this.records;

                if(data.length > 0)
                {
                    data = data.filter((row) => {
                        return  Object.keys(row).some((key) => {
                            return String(row[key]).toLowerCase().indexOf(this.quickSearchQuery.toLowerCase()) > -1 ;
                        });
                    });

                    data = _.orderBy(data, (i) => {
                        let value = i[this.sort.key];
                        if(!isNaN(parseFloat(value)))
                        {
                            return parseFloat(value);
                        }
                        return String(value).toLowerCase()
                    }, this.sort.order);
                }

                return data;
            }
        },
        methods: {
          getRecords(){
              axios.get(`${this.endpoint}?limit=${this.limit}`).then((res) => {
                  this.displayable = res.data.displayable;
                  this.records = res.data.data;
                  this.tableName = res.data.table;
                  }).catch( e => console.log(e));
            },
            sortBy(column)
            {
                this.sort.key = column;
                this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc';
            },
            edit(column, id)
            {
                this.editing.error = '';
                //this.editing.columnValue = "";

                if(column !== "id")
                {
                    if(id !== this.editing.id)
                    {
                        this.editing.id = id;
                        this.editing.column = column;
                    }
                    else
                    {
                        this.editing.id = null;
                        this.editing.column = "";
                    }
                }
            },
            update(event)
            {
                 let obj = {};
                 obj[this.editing.column] = event.target.value;
                 obj['column'] = this.editing.column;
                 obj['value'] = event.target.value;
                axios.patch(`${this.endpoint}/${this.editing.id}`, obj).then((data) => {
                    this.records.map((row)=>{
                        if(this.editing.id === row.id)
                        {
                            row[this.editing.column] = event.target.value;
                        }
                    });
                }).catch((error) => {
                    this.editing.error = error.response.data.errors[this.editing.column][0];
                });
            },
            debounce(event, method, timeout = 400)
            {
                if(this.timeoutId !== null)
                {
                    clearTimeout(this.timeoutId);
                }
                this.timeoutId = setTimeout( () => {
                   method(event);
                }, timeout);
            },
            search(event)
            {

                let obj = {};

                obj['limit'] = this.limit;

                this.search_col_vals[event.target.id] = event.target.value;

                obj['terms'] = this.search_col_vals;

                axios.post(`${this.endpoint}/search`, obj).then((res) => {
                    this.records = res.data;
                }).catch(e => console.log(e));

            }

        }
    }
</script>

<style lang="scss">
    .sortable{
        cursor: pointer;
    }
    .arrow{
        display: inline-block;
        vertical-align: middle;
        width:0;
        height: 0;
        margin-left: 5px;
        opacity: .6;

        &--asc{
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid #222;
        }

        &--desc{
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #222;
        }
    }
</style>
