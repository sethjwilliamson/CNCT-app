using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;

namespace WebApplication1.Controllers
{
    public class HomeController : Controller
    {
        public ActionResult Index()
        {
            return View();
        }

        
        public ActionResult About()
        {
            ViewBag.Message = "Why choose CNCT?";

            return View();
        }

        public ActionResult Contact()
        {
            ViewBag.Message = "Contact Information";

            return View();
        }

        [Authorize()]
        public ActionResult CNCT()
        {
            ViewBag.Message = "Welcome to CNCT";

            return View();
        }
    }
}