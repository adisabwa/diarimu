// import { ElNotification } from 'element-plus'

import { ElDatePicker } from 'element-plus';
import id from 'moment/src/locale/id';

export default {
  install: (app) => {
    // inject a globally available $translate() method
    app.use(ElDatePicker, { id });
    app.config.globalProperties.$notify = ElNotification
    app.config.globalProperties.$msgbox = ElMessageBox
    app.config.globalProperties.$alert = ElMessageBox.alert
    app.config.globalProperties.$confirm = ElMessageBox.confirm
    app.config.globalProperties.$propmt = ElMessageBox.prompt
  }
}