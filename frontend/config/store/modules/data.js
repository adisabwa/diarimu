import axios from "axios";
import { siteUrl } from "@/config/url"

const namespaced = true;

const state = {
  anggota: [],
  anggotas: [],
  pengawas: {},
  pengawass: [],
};

const actions = {
  getAllAnggotaInGroup: (context, payload) => {
    console.log(payload);
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "data/group/get_anggota",
        params: payload,
      }).then(response => {
        context.commit("ANGGOTAS_UPDATE", response.data);
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
  getAnggota: (context, payload) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "data/anggota/get/",
        params: payload,
      }).then(response => {
        context.commit("ANGGOTA_UPDATE", response.data);
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
  getAllPengawas: (context, payload) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "data/pengawas/get_all",
      }).then(response => {
        context.commit("MEMBERS_UPDATE", response.data.data);
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
  getPengawas: (context, params) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: siteUrl + "data/pengawas/get/",
        params: params,
      }).then(response => {
        context.commit("MEMBER_UPDATE", response.data.data);
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
};

const mutations = {
  ANGGOTA_UPDATE: (state, anggota) => {
    state.anggota = anggota;
  },
  ANGGOTAS_UPDATE: (state, anggotas) => {
    state.anggotas = anggotas;
  },
  MEMBERS_UPDATE: (state, pengawass) => {
    state.pengawass = pengawass;
  },
  MEMBER_UPDATE: (state, pengawas) => {
    state.pengawas = pengawas;
  }
};

const getters = {
  anggota: state  => state.anggota,
  anggotas: state  => state.anggotas,
  pengawass: state  => state.pengawass,
  pengawas: state  => state.pengawas,
};

export default {
  namespaced,
  state,
  getters,
  actions,
  mutations,
};
