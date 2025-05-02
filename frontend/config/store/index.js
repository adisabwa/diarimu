import { createStore } from 'vuex'
import configModule from './modules/config';
import filterModule from './modules/filter';
import authModule from './modules/auth';
import penggunaModule from './modules/pengguna';
import dataModule from './modules/data';

export default createStore({
  modules: {
    config: configModule,
    auth: authModule,
    filter: filterModule,
    pengguna: penggunaModule,
    data: dataModule,
  },
});
