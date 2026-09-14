
function showToast(message){
  var toast = document.getElementById("toast");
  if(!toast){
    toast = document.createElement("div");
    toast.id = "toast";
    toast.className = "toast";
    document.body.appendChild(toast);
  }
  toast.textContent = message;
  toast.classList.add("show");
  setTimeout(function(){ toast.classList.remove("show"); }, 2600);
}

function setError(form, name, message){
  var input = form.querySelector('[name="' + name + '"]');
  var error = form.querySelector('[data-error-for="' + name + '"]');
  if(input) input.classList.toggle("invalid", !!message);
  if(error) error.textContent = message || "";
  return !message;
}

function value(form, name){
  var el = form.querySelector('[name="' + name + '"]');
  return el ? el.value.trim() : "";
}

function validEmail(v){
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
}

function validPhone(v){
  return /^01[3-9]\d{8}$/.test(v);
}

function validPassword(v){
  return v.length >= 8 &&
         /[A-Z]/.test(v) &&
         /[a-z]/.test(v) &&
         /\d/.test(v) &&
         /[^A-Za-z0-9]/.test(v);
}

function validateForm(form){
  var type = form.dataset.validate;
  var ok = true;

  form.querySelectorAll(".error").forEach(function(e){ e.textContent = ""; });
  form.querySelectorAll(".invalid").forEach(function(e){ e.classList.remove("invalid"); });

  function req(name, label){
    var v = value(form, name);
    if(!v){ setError(form, name, label + " is required."); ok = false; }
    return v;
  }

  if(type === "register"){
    var name = req("name","Name");
    if(name && !/^[A-Za-z .'-]{3,50}$/.test(name)){ setError(form,"name","Use 3-50 letters/spaces only."); ok=false; }
    var email = req("email","Email");
    if(email && !validEmail(email)){ setError(form,"email","Enter a valid email address."); ok=false; }
    var phone = req("phone","Phone");
    if(phone && !validPhone(phone)){ setError(form,"phone","Use a valid 11-digit Bangladesh number."); ok=false; }
    req("role","Role");
    var pass = req("password","Password");
    if(pass && !validPassword(pass)){ setError(form,"password","Use 8+ chars with uppercase, lowercase, number and special character."); ok=false; }
    var confirmPass = req("confirm_password","Confirm password");
    if(confirmPass && pass !== confirmPass){ setError(form,"confirm_password","Passwords do not match."); ok=false; }
  }

  if(type === "login"){
    var email2 = req("email","Email");
    if(email2 && !validEmail(email2)){ setError(form,"email","Enter a valid email address."); ok=false; }
    req("password","Password");
  }

  if(type === "profile"){
    var n = req("name","Name");
    if(n && !/^[A-Za-z .'-]{3,50}$/.test(n)){ setError(form,"name","Use 3-50 letters/spaces only."); ok=false; }
    var e = req("email","Email");
    if(e && !validEmail(e)){ setError(form,"email","Enter a valid email address."); ok=false; }
    var p = req("phone","Phone");
    if(p && !validPhone(p)){ setError(form,"phone","Use a valid 11-digit Bangladesh number."); ok=false; }

    var cv = form.querySelector('[name="cv_file"]');
    if(cv && cv.files && cv.files.length){
      var file = cv.files[0];
      if(file.type !== "application/pdf"){
        setError(form,"cv_file","CV must be a PDF file.");
        ok=false;
      } else if(file.size > 2 * 1024 * 1024){
        setError(form,"cv_file","CV must be 2 MB or smaller.");
        ok=false;
      }
    }
  }

  if(type === "password"){
    req("current_password","Current password");
    var np = req("new_password","New password");
    if(np && !validPassword(np)){ setError(form,"new_password","Use 8+ chars with uppercase, lowercase, number and special character."); ok=false; }
    var cp = req("confirm_password","Confirm password");
    if(cp && np !== cp){ setError(form,"confirm_password","Passwords do not match."); ok=false; }
    if(value(form,"current_password") && value(form,"current_password") === np){
      setError(form,"new_password","New password must be different from current password.");
      ok=false;
    }
  }

  if(type === "application"){
    var cover = req("cover_note","Cover note");
    if(cover && cover.length < 20){ setError(form,"cover_note","Write at least 20 characters."); ok=false; }
    var sal = value(form,"expected_salary");
    if(sal && Number(sal) < 0){ setError(form,"expected_salary","Salary cannot be negative."); ok=false; }
  }

if(type === "job"){
    var jt = req("job_title","Job title");
    if(jt && jt.length < 3){
        setError(form,"job_title","Job title must be at least 3 characters.");
        ok=false;
    }

    req("category_id","Category");
    req("job_type","Job type");
    req("location","Location");

    var salary = req("salary","Salary");

    var deadline = req("deadline","Deadline");
    if(deadline){
        var today = new Date();
        today.setHours(0,0,0,0);

        var d = new Date(deadline + "T00:00:00");

        if(d < today){
            setError(form,"deadline","Deadline cannot be in the past.");
            ok=false;
        }
    }

    var desc = req("description","Description");
    if(desc && desc.length < 30){
        setError(form,"description","Write at least 30 characters.");
        ok=false;
    }
} //badhan part 

  if(type === "tip"){
    var tt = req("title","Title");
    if(tt && tt.length < 5){ setError(form,"title","Title must be at least 5 characters."); ok=false; }
    req("category","Category");
    var content = req("content","Content");
    if(content && content.length < 30){ setError(form,"content","Write at least 30 characters."); ok=false; }
  }

  if(type === "category"){
    var cn = req("category_name","Category name");
    if(cn && cn.length < 3){ setError(form,"category_name","Category name must be at least 3 characters."); ok=false; }
  }

  return ok;
}

document.addEventListener("DOMContentLoaded", function(){
  document.querySelectorAll("form[data-validate]").forEach(function(form){
    form.addEventListener("submit", function(e){
      if(!validateForm(form)){
        e.preventDefault();
        showToast("Please fix the highlighted fields.");
        return;
      }

      /* Prototype forms are intentionally stopped until that member connects
         the form to the role Controller. Real forms with method/action submit. */
      if(form.dataset.prototype === "true"){
        e.preventDefault();
        showToast(form.dataset.success || "Validation passed. Connect this form to the Controller next.");
      }
    });
  });

  document.querySelectorAll("[data-confirm]").forEach(function(btn){
    btn.addEventListener("click", function(e){
      e.preventDefault();
      var message = btn.dataset.confirm || "Are you sure?";
      if(confirm(message)){
        showToast(btn.dataset.success || "Action completed.");
      }
    });
  });

  document.querySelectorAll("[data-toast]").forEach(function(btn){
    btn.addEventListener("click", function(e){
      if(btn.getAttribute("href") === "#") e.preventDefault();
      showToast(btn.dataset.toast);
    });
  });

  var search = document.querySelector("[data-job-search]");
  if(search){
    search.addEventListener("input", function(){
      var q = search.value.toLowerCase();
      document.querySelectorAll("[data-job-row]").forEach(function(row){
        row.style.display = row.textContent.toLowerCase().includes(q) ? "" : "none";
      });
    });
  }
});
