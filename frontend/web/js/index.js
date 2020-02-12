const autoCompletejs = new autoComplete({
  data: {
    src: async function() {
      let query = document.querySelector("#autoComplete").value;
      if (query) {
        const source = await fetch("/address/" + query.replace(/ /g, '+'));
        const data = await source.json();

        return data;
      }

      return [];
    },
    key: ["city"],
    cache: false
  },
  trigger: {
    event: ["input", "focusin"]
  },
  threshold: 2,
  debounce: 300,
  searchEngine: "loose",
  highlight: false,
  maxResults: 5,
  resultsList: {
    render: true,
  },
  resultItem: {
    element: "p"
  },
  onSelection: function(feedback) {
    console.log('feedback --', feedback);
    document.querySelector("#autoComplete").blur();

    const point = feedback.selection.value.point.split(' ');
    document.querySelector("#long").value = point[0];
    document.querySelector("#lat").value = point[1];

    const selection = feedback.selection.value.city;
    document.querySelector("#autoComplete_list").innerHTML = selection;
    // Clear Input
    document.querySelector("#autoComplete").value = selection;
    // Change placeholder with the selected value
    document.querySelector("#autoComplete").setAttribute("placeholder", selection);
    // Concole log autoComplete data feedback
  },
});
