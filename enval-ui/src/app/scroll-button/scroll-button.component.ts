import { CommonModule } from '@angular/common';
import { Component, HostListener, OnInit } from '@angular/core';

@Component({
  selector: 'app-scroll-button',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './scroll-button.component.html',
  styleUrl: './scroll-button.component.css'
})
export class ScrollButtonComponent implements OnInit{
  isShown: boolean = false; 
  @HostListener('window:scroll', []) 
  onWindowScroll() {
    this.isShown = window.scrollY > 300; // Show button after 300px scroll 
  } 
  ngOnInit(){
    this.onWindowScroll();
  }
  scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); }

}
