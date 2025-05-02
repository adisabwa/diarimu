import axios from "axios";

const namespaced = true;

const state = {
  psb: [],
  psbs: [],
  pengawas: {},
  pengawass: [],
};

const actions = {
  getAllPsb: (context, payload) => {
    console.log(payload);
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: context.rootGetters.siteUrl + "data/psb/get_all",
        params: payload,
      }).then(response => {
        context.commit("PSBS_UPDATE", response.data.data);
        resolve(response);
      }).catch(error => {
        reject(error);
      });
    }); 
  },
  getPsb: (context, payload) => {
    return new Promise((resolve, reject) => {
      axios({
        method: "GET",
        url: context.rootGetters.siteUrl + "data/psb/get/",
        params: payload,
      }).then(response => {
        context.commit("PSB_UPDATE", response.data.data);
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
        url: context.rootGetters.siteUrl + "data/pengawas/get_all",
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
        url: context.rootGetters.siteUrl + "data/pengawas/get/",
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
  PSB_UPDATE: (state, psb) => {
    state.psb = psb;
  },
  PSBS_UPDATE: (state, psbs) => {
    state.psbs = psbs;
  },
  MEMBERS_UPDATE: (state, pengawass) => {
    state.pengawass = pengawass;
  },
  MEMBER_UPDATE: (state, pengawas) => {
    state.pengawas = pengawas;
  }
};

const getters = {
  psb: state  => state.psb,
  psbs: state  => state.psbs,
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
