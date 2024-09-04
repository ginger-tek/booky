import PicoVue from "https://unpkg.com/@ginger-tek/picovue@1.3.2/picovue.js";

const state = Vue.reactive({
  invoices: [],
  clients: [],
  template: "",
});

const idx = Vue.reactive({
  invoice: null,
  client: null,
});

const money = (n = 0) => {
  return Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(n);
};

const sumAmount = (inv) => {
  const sum = parseFloat(
    inv.items.reduce((a, b) => a + b.amount, 0.0).toFixed(2),
  );
  inv.amountDue = sum;
  return sum;
};

const sumExpenses = (inv) => {
  return parseFloat(
    inv.items
      .filter((e) => e.purchaseDate)
      .reduce((a, b) => a + b.amount, 0)
      .toFixed(2),
  );
};

const sumExpected = (inv) => {
  return inv.amountDue - sumExpenses(inv);
};

const uniqid = (prefix = '') => {
  return (prefix + crypto.randomUUID().replaceAll('-', '').toUpperCase()).slice(0, 8)
}

import "https://unpkg.com/chart.js@4.4.4/dist/chart.umd.js";
const Charty = {
  props: {
    type: {
      type: String,
      default: "line",
    },
    data: {
      type: Object,
      default: {},
    },
    options: {
      type: Object,
      default: {
        elements: {
          line: {
            borderJoinStyle: 'round'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    },
  },
  template: `<div class="overflow-auto" style="position:relative;height:50vh;width:100%">
    <canvas ref="el" />
  </div>`,
  setup(props) {
    const el = Vue.ref(null);
    let inst = null;

    function init() {
      if (inst) inst.destroy();
      inst = new Chart(el.value, {
        type: props.type,
        data: props.data,
        options: {
          maintainAspectRatio: false,
          ...props.options,
        },
      });
    }

    Vue.watch(() => props.type, init);
    Vue.watch(() => props.options, init, { deep: true });
    Vue.watch(
      () => props.data,
      () => {
        if (!inst) return;
        inst.data = props.data;
        inst.update();
      },
      { deep: true },
    );

    Vue.onMounted(init);

    return { el };
  },
};

Vue.createApp({
  components: { Charty },
  setup() {
    const appendToast = Vue.inject("pv_appendToast");

    const toggleCreateInvoice = Vue.ref(false);
    const toggleEditInvoice = Vue.ref(false);
    const toggleCreateInvoiceItem = Vue.ref(false);
    const toggleCreateClient = Vue.ref(false);
    const toggleEditClient = Vue.ref(false);

    const selectedYear = Vue.ref(new Date().getFullYear());
    const saveState = Vue.ref(false);
    const saving = Vue.ref(false);
    const loading = Vue.ref(true);

    const invoiceTableFields = [
      "summary",
      "clientId",
      "dueDate",
      "paidDate",
      { name: "amountDue", label: "Due / Paid" },
    ];
    const clientTableFields = ["name", "email", "phone"];
    const expenseTableFields = ["summary", "type", "purchaseDate", "amount"];

    async function getData() {
      try {
        const { invoices, clients, template } = await fetch("/data").then((r) =>
          r.json(),
        );
        state.invoices = invoices;
        state.clients = clients;
        state.template = template;
      } catch (ex) {
        appendToast("Failed to get data", { type: "error" });
      } finally {
        loading.value = false;
      }
    }

    async function putData() {
      saving.value = true;
      try {
        const { result } = await fetch("/data", {
          method: "PUT",
          body: JSON.stringify(state),
        }).then((r) => r.json());
        saveState.value = result;
        setTimeout(() => (saveState.value = false), 3000);
      } catch (ex) {
        appendToast(ex.toString(), { type: "error" });
      } finally {
        saving.value = false;
      }
    }

    let bounce;
    function autoSaveForm(e) {
      if (bounce) clearTimeout(bounce);
      bounce = setTimeout(() => {
        const form = e.target.closest("form");
        if (!form.checkValidity()) return form.reportValidity();
        form.dispatchEvent(new Event("submit"));
      }, 1000);
    }

    function createClient(e) {
      toggleCreateClient.value = false;
      state.clients.push({
        id: uniqid(),
        name: e.target.name.value,
        email: null,
        phone: null,
        address: null,
        company: null,
        created: Date.now(),
        updated: Date.now(),
      });
      idx.client = state.clients.length - 1;
      e.target.reset();
      if (!toggleCreateInvoice.value)
        toggleEditClient.value = true;
      Vue.nextTick(putData);
    }

    function createInvoice(e) {
      toggleCreateInvoice.value = false;
      state.invoices.push({
        id: uniqid('INV'),
        summary: e.target.summary.value,
        clientId: e.target.clientId.value,
        details: null,
        amountDue: 0,
        dueDate: null,
        amountPaid: 0,
        paidDate: null,
        created: Date.now(),
        updated: Date.now(),
        items: [],
      });
      idx.invoice = state.invoices.length - 1;
      e.target.reset();
      toggleEditInvoice.value = true;
      Vue.nextTick(putData);
    }

    function createInvoiceItem(e) {
      toggleCreateInvoiceItem.value = false;
      state.invoices[idx.invoice].items.push({
        id: uniqid(),
        summary: e.target.summary.value,
        type: e.target.type.value,
        amount: parseFloat(e.target.amount.value),
        purchaseDate: e.target.purchaseDate.value,
        created: Date.now(),
        updated: Date.now(),
      });
      e.target.reset();
      toggleCreateInvoiceItem.value = false;
      Vue.nextTick(putData);
    }

    function editClient(c) {
      idx.client = state.clients.findIndex(({ id }) => id == c.id);
      toggleEditClient.value = true;
    }

    function deleteClient() {
      if (
        !confirm(
          `Are you sure you want to delete ${state.clients[idx.client].name}?`,
        )
      )
        return;
      const refs = state.invoices.some(
        (i) => i.clientId == state.clients[idx.client].id,
      );
      if (refs) {
        alert(
          `${state.clients[idx.client].name} is assigned as the client to existing invoices. They must first be unassigned from any invoices first before they are deleted`,
        );
        return;
      }
      toggleEditClient.value = false;
      setTimeout(async () => {
        state.clients.splice(idx.client, 1);
        idx.client = null;
        Vue.nextTick(putData);
        appendToast("Client deleted", { type: "success" });
      }, 500);
    }

    function editInvoice(i) {
      idx.invoice = state.invoices.findIndex(({ id }) => id == i.id);
      toggleEditInvoice.value = true;
    }

    function removeItem(id) {
      state.invoices[idx.invoice].items.splice(
        state.invoices[idx.invoice].items.findIndex((i) => i.id == id),
        1,
      );
      Vue.nextTick(putData);
    }

    function openPrintPreview() {
      if (idx.invoice == null) return
      const win = window.open("about:blank", "_blank");
      win.document.write(rendered.value);
      win.print()
      win.close()
    }

    function deleteInvoice() {
      if (
        !confirm(
          `Are you sure you want to delete ${state.invoices[idx.invoice].summary}?`,
        )
      )
        return;
      toggleEditInvoice.value = false;
      setTimeout(async () => {
        state.invoices.splice(idx.invoice, 1);
        Vue.nextTick(putData);
        idx.invoice = null;
        appendToast("Invoice deleted", { type: "success" });
      }, 200);
    }

    function onClose(r) {
      setTimeout(() => (r = null), 201);
    }

    function insertRef(e) {
      state.template += `\n${e.target.innerText}`
      e.target.closest('details').removeAttribute('open')
      Vue.nextTick(() => document.getElementById('markup').focus())
    }

    const expenses = Vue.computed(() => {
      return state.invoices.reduce(
        (a, b) => [...a, ...b.items.filter((e) => !!e.purchaseDate)],
        [],
      );
    });

    const rendered = Vue.computed(() => {
      if (idx.invoice == null) return;
      const invoice = state.invoices[idx.invoice];
      const client = state.clients[state.clients.findIndex((c) => c.id == invoice.clientId)];
      const itemsTable = `<table style="width:100%">
        <tr>
          <th align="left">Summary</th>
          <th align="left">Type</th>
          <th align="right">Amount</th>
        </tr>
        ${invoice.items.map(
        (i) => `<tr>
            <td>${i.summary}</td>
            <td>${i.type}</td>
            <td align="right">${money(i.amount)}</td>
          </tr>`
      ).join("\n")}
        <tr>
          <th colspan="2" align="right">Total</th>
          <td align="right"><b>${money(invoice.amountDue)}</b></td>
        </tr>
      </table>`;
      return `<title>Invoice - ${state.invoices[idx.invoice].id}</title>
      <style>
      body {
        max-inline-size: 8.5in;
        margin-inline: auto;
        aspect-ratio: 8.5/11;
      }
      </style>
      ${state.template
          .replaceAll(/\[invoice.(\w+)\]/g, (_m, p) => invoice[p] || '')
          .replaceAll(/\[client.(\w+)\]/g, (_m, p) => client[p] || '')
          .replaceAll(/\[itemsTable\]/g, itemsTable)}`
    });

    const preview = Vue.computed(() => {
      return 'data:text/html;base64,' + btoa(state.template)
    })

    const years = Vue.computed(() => {
      let s = new Date().getFullYear() - 4;
      return Array.from(Array(5), (_v, i) => s + i);
    });

    const months = Vue.computed(() => {
      return Array.from(Array(12), (_v, i) => {
        return new Date(selectedYear.value, i, 1, 0, 0, 0, 0).toLocaleString(
          "en-US",
          { month: "short" },
        );
      });
    });

    const monthToMonth = Vue.computed(() => {
      const revenue = months.value.map((_m, x) => {
        return state.invoices
          .filter((i) => {
            const dt = new Date(i.paidDate);
            return (
              i.paidDate &&
              dt.getMonth() == x &&
              dt.getFullYear() == selectedYear.value
            );
          })
          .reduce((a, b) => a + b.amountPaid, 0);
      });
      const expenses = months.value.map((_m, x) => {
        return state.invoices.reduce(
          (a, b) =>
            a +
            b.items
              .filter((e) => {
                const dt = new Date(e.purchaseDate);
                return (
                  e.purchaseDate &&
                  dt.getMonth() == x &&
                  dt.getFullYear() == selectedYear.value
                );
              })
              .reduce((c, d) => c + d.amount, 0),
          0,
        );
      });
      const income = months.value.map((_m, x) => {
        return revenue[x] - expenses[x];
      });
      return {
        labels: months.value,
        datasets: [
          {
            label: "Revenue",
            data: revenue,
          },
          {
            label: "Expenses",
            data: expenses,
          },
          {
            label: "Income",
            data: income,
          },
        ],
      };
    });

    const yearToYear = Vue.computed(() => {
      const revenue = years.value.map((y) => {
        return state.invoices
          .filter((i) => i.paidDate && new Date(i.paidDate).getFullYear() == y)
          .reduce((a, b) => a + b.amountPaid, 0);
      });
      const expenses = years.value.map((y) => {
        return state.invoices.reduce(
          (a, b) =>
            a +
            b.items
              .filter(
                (e) =>
                  e.purchaseDate && new Date(e.purchaseDate).getFullYear() == y,
              )
              .reduce((c, d) => c + d.amount, 0),
          0,
        );
      });
      const income = years.value.map((_y, x) => {
        return revenue[x] - expenses[x];
      });
      return {
        labels: years.value,
        datasets: [
          {
            label: "Revenue",
            data: revenue,
          },
          {
            label: "Expenses",
            data: expenses,
          },
          {
            label: "Income",
            data: income,
          },
        ],
      };
    });

    Vue.onBeforeMount(getData);
    Vue.onMounted(() => (app.style.opacity = 1));

    return {
      state,
      saveState,
      saving,
      loading,

      idx,
      expenses,
      invoiceTableFields,
      clientTableFields,
      expenseTableFields,
      rendered,
      preview,

      monthToMonth,
      yearToYear,
      selectedYear,
      years,

      getData,
      putData,
      autoSaveForm,
      toggleCreateClient,
      createClient,
      toggleEditClient,
      toggleCreateInvoice,
      createInvoice,
      toggleEditInvoice,
      toggleCreateInvoiceItem,
      openPrintPreview,
      createInvoiceItem,
      removeItem,
      editClient,
      deleteClient,
      editInvoice,
      deleteInvoice,

      sumAmount,
      sumExpenses,
      sumExpected,
      money,
      onClose,
      insertRef
    };
  },
})
  .use(PicoVue)
  .mount("#app");
